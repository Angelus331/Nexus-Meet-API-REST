<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_resets';

    protected $fillable = [
        'correo',
        'codigo',
        'expira_en'
    ];

    protected $casts = [
        'expira_en' => 'datetime'
    ];
}
