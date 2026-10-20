<?php

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Notifies the studio that a new contact / offerte request came in. */
class ContactRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactRequest $contactRequest)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Nieuw contactformulier: '.($this->contactRequest->subject ?: $this->contactRequest->name))
            ->replyTo($this->contactRequest->email, $this->contactRequest->name)
            ->markdown('emails.contact-request');
    }
}
