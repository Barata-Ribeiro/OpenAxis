<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserMail extends Mailable
{
    use Queueable, SerializesModels;

    private $appName;

    /**
     * Create a new message instance.
     */
    public function __construct(private $name, private $email, private $password)
    {
        $this->appName = config('app.name');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->appName.' - Your new account');
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new_account',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'appName' => $this->appName,
            ],
        );
    }
}
