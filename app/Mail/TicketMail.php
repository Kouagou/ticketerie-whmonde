<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $ticket;
    public $event;
    public $qrCode;

    public function __construct(Participant $participant, Ticket $ticket, Event $event)
    {
        $this->participant = $participant;
        $this->ticket = $ticket;
        $this->event = $event;

        // Générer un QR code pour le ticket
        $this->qrCode = base64_encode(QrCode::format('png')
            ->size(200)
            ->generate($ticket->ticket_code));
    }

    public function build()
    {
        return $this->subject('Votre ticket pour ' . $this->event->title)
            ->view('emails.ticket');
    }
}
