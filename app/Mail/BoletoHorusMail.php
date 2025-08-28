<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BoletoHorusMail extends Mailable
{
    use Queueable, SerializesModels;


    public $boleto;
    public $notas; // notas fiscais do boleto

    protected $filePath;

    public $logoPath;

    public function __construct($boleto, $notas = [], $filePath)
    {
        $this->boleto = $boleto;
        $this->notas = $notas;
        $this->filePath = $filePath;
        $this->logoPath = public_path('images/LOGOcor.jpg');
    }

    public function build()
    {
        $email =  $this->subject('Boleto Martin Claret')
            ->view('emails.boletos') // sua view de e-mail
            ->attach($this->filePath)
            ->with([
                'logoPath' => $this->logoPath,
            ]);

        foreach ($this->notas as $nota) {

            if (file_exists($nota['path_nota'])) {
                $email->attach($nota['path_nota'], [
                    'as' => "Nota_{$nota['numero']}.pdf",
                    'mime' => 'application/pdf'
                ]);
            }
        }

        return $email;
    }
}
