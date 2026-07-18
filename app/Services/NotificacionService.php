<?php

namespace App\Services;

use App\Models\Circulo;
use App\Models\Notificacion;
use App\Models\Usuario;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class NotificacionService
{
    public function __construct(private Messaging $messaging)
    {
    }

    /**
     * Crea el registro de notificación (historial en Postgres, ya consumido por
     * GET /notificaciones) y, si el usuario tiene un fcm_token guardado, además
     * le manda el push aunque no tenga la app abierta.
     */
    public function enviar(
        Usuario $usuario,
        string $tipo,
        string $titulo,
        ?string $contenido = null,
        ?Circulo $circulo = null
    ): Notificacion {
        $notificacion = Notificacion::create([
            'usuario_id' => $usuario->id,
            'circulo_id' => $circulo?->id,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'contenido' => $contenido,
            'leida' => false,
        ]);

        if ($usuario->fcm_token) {
            $this->enviarPush($usuario->fcm_token, $titulo, $contenido, $tipo, $circulo);
        }

        return $notificacion;
    }

    private function enviarPush(string $token, string $titulo, ?string $contenido, string $tipo, ?Circulo $circulo): void
    {
        try {
            $mensaje = CloudMessage::withTarget('token', $token)
                ->withNotification(FirebaseNotification::create($titulo, $contenido ?? ''))
                ->withData([
                    'tipo' => $tipo,
                    'circulo_id' => $circulo?->id !== null ? (string) $circulo->id : '',
                ]);

            $this->messaging->send($mensaje);
        } catch (\Throwable $e) {
            // Un token vencido/inválido no debe tumbar la petición HTTP que disparó esto;
            // el historial en Postgres ya quedó guardado arriba de todas formas.
            report($e);
        }
    }
}