<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateEventStatus extends Command
{
    protected $signature = 'events:update-status';
    protected $description = 'Met à jour le statut des événements expirés';

    public function handle()
    {
        $now = Carbon::now();

        // Trouver tous les événements actifs dont la date de fin est passée
        $expiredEvents = Event::where('status', 'active')
            ->where('end_date', '<', $now->format('Y-m-d'))
            ->where('end_date', '>=', $now->copy()->subDay()->format('Y-m-d'))
            ->get();

        foreach ($expiredEvents as $event) {
            // Vérifier si l'heure est 23:59
            $endOfDay = Carbon::parse($event->end_date)->endOfDay();

            if ($now->greaterThanOrEqualTo($endOfDay)) {
                $event->status = 'expired';
                $event->save();

                $this->info("Événement #{$event->id} ({$event->title}) marqué comme expiré.");
            }
        }

        $this->info('Mise à jour du statut des événements terminée.');
    }
}
