<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $title;
    public $description;

    public function __construct($email, $title, $description)
    {
        $this->email = $email;
        $this->title = $title;
        $this->description = $description;
    }

    public function build()
    {
        $body = "
            <p><strong>Sender Email:</strong> {$this->email}</p>
            <p><strong>Message:</strong></p>
            <p>{$this->description}</p>
        ";

        return $this->subject($this->title)
            ->html($body);
    }
}
