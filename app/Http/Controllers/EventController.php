<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Muestra la lista de eventos próximos.
     */
    public function index()
    {
        // SOLUCIÓN DEFINITIVA DE HORARIO:
        // Definimos el "umbral" como el inicio del día de hoy (00:00 AM).
        // Usamos subDay() por seguridad para cubrir diferencias de zona horaria extremas.
        // Así, cualquier evento de "Ayer" en adelante se muestra como activo.
        $threshold = now()->subDay()->startOfDay();

        // 1. Eventos Próximos (De ayer, hoy y futuro)
        $upcomingEvents = Event::where('start_time', '>=', $threshold)
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        // 2. Eventos Pasados (Solo los realmente antiguos)
        $pastEvents = Event::where('start_time', '<', $threshold)
            ->orderBy('start_time', 'desc')
            ->take(5)
            ->get();

        return view('events.index', compact('upcomingEvents', 'pastEvents'));
    }

    /**
     * Muestra los eventos a los que el usuario asistirá.
     */
    public function myEvents()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $myEvents = $user->attendingEvents()
                         ->orderBy('start_time', 'desc')
                         ->paginate(10);

        return view('events.my_events', compact('myEvents'));
    }

    /**
     * Guarda un nuevo evento.
     */
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

    /**
     * Permite al usuario asistir (o cancelar asistencia) a un evento.
     */
    public function toggleAttendance(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Cancelar asistencia
        if ($event->attendees()->where('user_id', $user->id)->exists()) {
            $event->attendees()->detach($user->id);
            return back()->with('success', 'Has cancelado tu asistencia.');
        }

        // 2. Verificar cupos
        if ($event->spots_left === 0) {
            return back()->with('error', '¡Lo sentimos! Ya no hay cupos disponibles.');
        }

        // 3. Inscribir
        $event->attendees()->attach($user->id);

        return back()->with('success', '¡Te has inscrito al evento correctamente!');
    }
}