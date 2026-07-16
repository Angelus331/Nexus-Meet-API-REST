<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public string $codigo;

    public function __construct(string $codigo)
    {
        $this->codigo = $codigo;
    }

    public function build()
    {
        return $this
            ->subject('Recuperación de contraseña - NexusMeet')
            ->view('emails.password-reset');
    }
}
