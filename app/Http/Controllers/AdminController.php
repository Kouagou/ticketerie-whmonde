<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalEvents' => Event::count(),
            'activeEvents' => Event::where('status', 'active')->count(),
            'expiredEvents' => Event::where('status', 'expired')->count(),
            'totalParticipants' => Participant::count(),
        ];

        $recentEvents = Event::withCount('participants')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->each(function ($event) {
                $event->current_participants = $event->participants_count;
            });

        return response()->json([
            'stats' => $stats,
            'recentEvents' => $recentEvents,
        ]);
    }

    public function statistics()
    {
        // Statistiques générales
        $stats = [
            'totalEvents' => Event::count(),
            'totalParticipants' => Participant::count(),
            'averageParticipantsPerEvent' => Participant::count() > 0 ? round(Participant::count() / Event::count()) : 0,
        ];

        // Calcul du taux de participation moyen
        $events = Event::withCount('participants')->get();
        $totalCapacity = $events->sum('max_participants');
        $totalParticipants = $events->sum('participants_count');
        $stats['participationRate'] = $totalCapacity > 0 ? round(($totalParticipants / $totalCapacity) * 100) : 0;

        // Événements par mois
        $eventsByMonth = DB::table('events')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Participants par événement
        $participantsByEvent = Event::withCount('participants')
            ->select('id', 'title', 'max_participants')
            ->orderBy('participants_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($event) {
                return [
                    'name' => $event->title,
                    'participants' => $event->participants_count,
                    'capacity' => $event->max_participants,
                ];
            });

        return response()->json([
            'stats' => $stats,
            'eventsByMonth' => $eventsByMonth,
            'participantsByEvent' => $participantsByEvent,
        ]);
    }
}
