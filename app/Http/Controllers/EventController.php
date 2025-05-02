<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('participants')->get();

        // Ajouter le nombre de participants actuel à chaque événement
        $events->each(function ($event) {
            $event->current_participants = $event->participants_count;
        });

        return response()->json($events);
    }

    public function show($id)
    {
        $event = Event::withCount('participants')->findOrFail($id);
        $event->current_participants = $event->participants_count;

        return response()->json($event);
    }

    public function store(EventRequest $request)
    {
        $event = Event::create($request->validated());

        return response()->json($event, 201);
    }

    public function update(EventRequest $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->validated());

        return response()->json($event);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete(); // Soft delete

        return response()->json(null, 204);
    }

    public function participants($id)
    {
        $event = Event::findOrFail($id);
        $participants = $event->participants()->with('ticket')->get();

        return response()->json($participants);
    }
}
