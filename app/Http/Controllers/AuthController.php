<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * POST /auth/register
     * Registro clásico con nombre, correo y password.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|max:150|unique:usuario,correo',
            'password' => 'required|string|min:8',
        ]);

        $usuario = Usuario::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'correo' => $data['correo'],
            'password_hash' => Hash::make($data['password']),
        ]);

        $token = JWTAuth::fromUser($usuario);

        return response()->json([
            'data' => $usuario,
            'token' => $token,
        ], 201);
    }

    /**
     * POST /auth/login
     * Login clásico con correo y password.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('correo', $data['correo'])->first();

        if (!$usuario || !Hash::check($data['password'], $usuario->password_hash)) {
            return response()->json([
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        if (!$usuario->activo) {
            return response()->json([
                'message' => 'Esta cuenta está desactivada',
            ], 403);
        }

        $token = JWTAuth::fromUser($usuario);

        return response()->json([
            'data' => $usuario,
            'token' => $token,
        ]);
    }

    /**
     * POST /auth/google
     * Login o registro automático usando un id_token de Firebase (Google Sign-In).
     * Firebase solo confirma la identidad; los datos siguen viviendo en Postgres.
     */
    public function loginGoogle(Request $request, FirebaseAuth $firebaseAuth): JsonResponse
    {
        $data = $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $verifiedIdToken = $firebaseAuth->verifyIdToken($data['id_token']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Token de Google inválido o expirado',
            ], 401);
        }

        $claims = $verifiedIdToken->claims();
        $googleId = $claims->get('sub');
        $correo = $claims->get('email');
        $nombreCompleto = $claims->get('name', $correo);
        $foto = $claims->get('picture');

        $usuario = Usuario::where('google_id', $googleId)
            ->orWhere('correo', $correo)
            ->first();

        if (!$usuario) {
            [$nombre, $apellido] = $this->separarNombre($nombreCompleto);

            $usuario = Usuario::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'google_id' => $googleId,
                'foto_perfil_url' => $foto,
                // La columna es NOT NULL pero el login es solo por Google, nunca se usa
                'password_hash' => Hash::make(Str::random(40)),
                'email_verificado_en' => now(),
            ]);
        } elseif (!$usuario->google_id) {
            // Ya existía con correo/password y ahora vincula su cuenta de Google
            $usuario->update([
                'google_id' => $googleId,
                'email_verificado_en' => $usuario->email_verificado_en ?? now(),
            ]);
        }

        if (!$usuario->activo) {
            return response()->json([
                'message' => 'Esta cuenta está desactivada',
            ], 403);
        }

        $token = JWTAuth::fromUser($usuario);

        return response()->json([
            'data' => $usuario,
            'token' => $token,
        ]);
    }

    /**
     * GET /auth/me
     * Devuelve el usuario autenticado a partir del JWT.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }

    /**
     * POST /auth/logout
     * Invalida el token JWT actual.
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            // El token ya era inválido o no se envió; no es un error grave para el usuario
        }

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    /**
     * POST /auth/refresh
     * Renueva el token JWT antes de que expire.
     */
    public function refresh(): JsonResponse
    {
        try {
            $nuevoToken = JWTAuth::refresh(JWTAuth::getToken());
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'No se pudo renovar el token, vuelve a iniciar sesión',
            ], 401);
        }

        return response()->json([
            'token' => $nuevoToken,
        ]);
    }

    /**
     * Separa "Juan Pérez López" en ['Juan', 'Pérez López'] para encajar
     * en las columnas nombre/apellido cuando el dato viene de Google.
     */
    private function separarNombre(string $nombreCompleto): array
    {
        $partes = explode(' ', trim($nombreCompleto), 2);

        return [
            $partes[0] ?? $nombreCompleto,
            $partes[1] ?? '',
        ];
    }
 
   public function forgotPassword(Request $request): JsonResponse
   {
    $data = $request->validate([
        'correo' => 'required|email',
    ]);

    $usuario = Usuario::where('correo', $data['correo'])->first();

    // Por seguridad no revelamos si el correo existe o no
    if (!$usuario) {
        return response()->json([
            'message' => 'Si el correo existe, se enviará un código de recuperación.'
        ]);
    }

    // Eliminar códigos anteriores
    PasswordReset::where('correo', $usuario->correo)->delete();

    // Generar código de 6 dígitos
    $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    PasswordReset::create([
        'correo' => $usuario->correo,
        'codigo' => $codigo,
        'expira_en' => Carbon::now()->addMinutes(15),
    ]);

    Mail::to($usuario->correo)->send(new PasswordResetMail($codigo));

    return response()->json([
        'message' => 'Si el correo existe, se enviará un código de recuperación.'
    ]);
   }
   
   public function resetPassword(Request $request): JsonResponse
   {
    $data = $request->validate([
        'correo' => 'required|email',
        'codigo' => 'required|string|size:6',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $registro = PasswordReset::where('correo', $data['correo'])
        ->where('codigo', $data['codigo'])
        ->first();

    if (!$registro) {
        return response()->json([
            'message' => 'Código incorrecto.'
        ], 400);
    }

    if (Carbon::now()->greaterThan($registro->expira_en)) {

        $registro->delete();

        return response()->json([
            'message' => 'El código ha expirado.'
        ], 400);
    }

    $usuario = Usuario::where('correo', $data['correo'])->first();

    if (!$usuario) {
        return response()->json([
            'message' => 'Usuario no encontrado.'
        ], 404);
    }

    $usuario->password_hash = Hash::make($data['password']);
    $usuario->save();

    // Eliminar el código para que no pueda reutilizarse
    $registro->delete();

    return response()->json([
        'message' => 'Contraseña actualizada correctamente.'
    ]);
   }
}
