<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GastoDetalle extends Model
{
    protected $table = 'gasto_detalle';

    // No tiene created_at ni updated_at
    public $timestamps = false;

    protected $fillable = [
        'gasto_id',
        'usuario_id',
        'monto_asignado',
        'pagado',
        'fecha_pago',
    ];

    protected $casts = [
        'monto_asignado' => 'decimal:2',
        'pagado' => 'boolean',
        'fecha_pago' => 'datetime',
    ];

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class, 'gasto_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
