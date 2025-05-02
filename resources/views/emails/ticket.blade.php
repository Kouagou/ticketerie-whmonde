<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Votre ticket pour {{ $event->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .ticket {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .ticket-info {
            margin-bottom: 20px;
        }
        .ticket-info p {
            margin: 5px 0;
        }
        .ticket-qr {
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<h1>Votre inscription est confirmée !</h1>

<p>Bonjour {{ $participant->first_name }} {{ $participant->last_name }},</p>

<p>Nous vous confirmons votre inscription à l'événement <strong>{{ $event->title }}</strong>.</p>

<div class="ticket">
    <div class="ticket-header">
        <h2>{{ $event->title }}</h2>
        <p>Ticket #{{ $ticket->ticket_code }}</p>
    </div>

    <div class="ticket-info">
        <p><strong>Participant :</strong> {{ $participant->first_name }} {{ $participant->last_name }}</p>
        <p><strong>Email :</strong> {{ $participant->email }}</p>
        <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('d/m/Y H:i') }}</p>
    </div>

    <div class="ticket-qr">
        <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
        <p>Présentez ce QR code à l'entrée de l'événement</p>
    </div>
</div>

<p>Nous vous remercions pour votre inscription et nous avons hâte de vous accueillir !</p>

<div class="footer">
    <p>Ce ticket est personnel et ne peut être transféré. Pour toute question, veuillez nous contacter.</p>
    <p>© {{ date('Y') }} Ticketerie - Tous droits réservés</p>
</div>
</body>
</html>
