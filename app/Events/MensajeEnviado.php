<?php

namespace App\Events;

use App\Models\MensajeChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajeEnviado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public MensajeChat $mensaje)
    {
    }

    /**
     * A qué canal se transmite: uno por círculo, para que solo
     * sus miembros puedan escuchar (autorización en routes/channels.php).
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('circulo.' . $this->mensaje->circulo_id),
        ];
    }

    /**
     * Nombre del evento tal como lo escucha Flutter/el panel web.
     */
    public function broadcastAs(): string
    {
        return 'MensajeEnviado';
    }

    /**
     * Payload a mano, en vez de serializar el modelo completo:
     * evita exponer campos internos y controla exactamente qué viaja por el socket.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->mensaje->id,
            'circulo_id' => $this->mensaje->circulo_id,
            'contenido' => $this->mensaje->contenido,
            'tipo' => $this->mensaje->tipo,
            'usuario' => [
                'id' => $this->mensaje->usuario->id,
                'nombre' => $this->mensaje->usuario->nombre,
                'foto_perfil_url' => $this->mensaje->usuario->foto_perfil_url,
            ],
            'created_at' => $this->mensaje->created_at,
        ];
    }
}