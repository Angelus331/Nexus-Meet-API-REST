<?php

namespace App\Console\Commands;

use App\Models\Evento;
use App\Services\NotificacionService;
use Illuminate\Console\Command;

class NotificarEventosProximos extends Command
{
    protected $signature = 'uniconecta:notificar-eventos-proximos';
    protected $description = 'Avisa a los miembros de eventos que empiezan en la próxima hora';

    public function handle(NotificacionService $notificaciones): void
    {
        $eventos = Evento::where('estado', 'programado')
            ->whereBetween('fecha_hora', [now(), now()->addHour()])
            ->get();

        foreach ($eventos as $evento) {
            foreach ($evento->circulo->miembros as $miembro) {
                $notificaciones->enviar(
                    usuario: $miembro,
                    tipo: 'evento_proximo',
                    titulo: 'Un evento está por comenzar',
                    contenido: "{$evento->titulo} empieza pronto",
                    circulo: $evento->circulo,
                );
            }
        }

        $this->info("Notificados {$eventos->count()} evento(s) próximo(s).");
    }
}