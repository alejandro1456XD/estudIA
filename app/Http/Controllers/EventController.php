<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    
    public function index()
    {
        
        $threshold = now()->subDay()->startOfDay();

        
        $upcomingEvents = Event::where('start_time', '>=', $threshold)
            ->orderBy('start_time', 'asc')
            ->paginate(10);

       
        $pastEvents = Event::where('start_time', '<', $threshold)
            ->orderBy('start_time', 'desc')
            ->take(5)
            ->get();

        return view('events.index', compact('upcomingEvents', 'pastEvents'));
    }

   
    public function myEvents()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $myEvents = $user->attendingEvents()
                         ->orderBy('start_time', 'desc')
                         ->paginate(10);

        return view('events.my_events', compact('myEvents'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_time' => 'required|date', 
            'location' => 'required|string|max:100',
            'type' => 'required|string',
            'max_attendees' => 'nullable|integer|min:1',
        ]);

        Event::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'location' => $request->location,
            'type' => $request->type,
            'max_attendees' => $request->max_attendees,
            'is_virtual' => $request->has('is_virtual'),
        ]);

        return redirect()->route('events')->with('success', '¡Evento creado exitosamente!');
    }

   
    public function toggleAttendance(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        
        if ($event->attendees()->where('user_id', $user->id)->exists()) {
            $event->attendees()->detach($user->id);
            return back()->with('success', 'Has cancelado tu asistencia.');
        }

        
        if ($event->spots_left === 0) {
            return back()->with('error', '¡Lo sentimos! Ya no hay cupos disponibles.');
        }

        
        $event->attendees()->attach($user->id);

        return back()->with('success', '¡Te has inscrito al evento correctamente!');
    }
}