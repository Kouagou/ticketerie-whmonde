<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipantRequest;
use App\Mail\TicketMail;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    public function register(ParticipantRequest $request)
    {
        $event = Event::findOrFail($request->event_id);

        // Vérifier si l'événement est actif
        if ($event->status !== 'active') {
            return response()->json(['error' => 'Cet événement est expiré.'], 400);
        }

        // Vérifier si l'événement est complet
        if ($event->isFullyBooked()) {
            return response()->json(['error' => 'Cet événement est complet.'], 400);
        }

        // Créer le participant
        $participant = Participant::create([
            'event_id' => $request->event_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        // Générer un ticket unique
        $ticketCode = Str::random(10) . '-' . $event->id . '-' . $participant->id;

        $ticket = Ticket::create([
            'participant_id' => $participant->id,
            'ticket_code' => $ticketCode,
            'is_used' => false,
        ]);

        // Envoyer le ticket par email
        Mail::to($participant->email)->send(new TicketMail($participant, $ticket, $event));

        return response()->json([
            'message' => 'Inscription réussie. Un ticket a été envoyé à votre adresse email.',
            'participant' => $participant,
            'ticket' => $ticket,
        ], 201);
    }
}
