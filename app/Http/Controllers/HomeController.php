<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $friends = $user ? $user->all_friends : collect();
        $posts = Post::with(['user', 'likes', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('home', compact('posts', 'friends'));
    }

    public function friends() { return view('friends'); }
    public function groups() { return view('groups'); }
    
    // --- SECCIÓN DE CURSOS ---
    public function courses() 
    { 
        /** @var \App\Models\User $user */
        $user = Auth::user();

        
        $myCreatedCourses = Course::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        
        $enrolledCourses = $user->enrolledCourses;

        
        $availableCourses = Course::where('user_id', '!=', $user->id)
            ->whereDoesntHave('students', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('courses', compact('myCreatedCourses', 'enrolledCourses', 'availableCourses')); 
    }

    public function events() { return view('events'); }
    public function resources() { return view('resources'); }

    
    public function storeCourse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'course_type' => 'required|string',
        ]);

        $schedule = null;
        if ($request->course_type === 'live' || $request->course_type === 'hybrid') {
            $schedule = [
                'days' => $request->input('days', []),
                'start_time' => $request->input('start_time'),
                'duration' => $request->input('duration'),
            ];
        }

        Course::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'category' => $request->category,
            'language' => $request->language,
            'level' => $request->level,
            'description' => $request->description,
            'type' => $request->course_type,
            'schedule' => $schedule,
            'is_published' => true
        ]);

        return back()->with('success', '¡Curso "' . $request->name . '" creado exitosamente!');
    }

   
    public function enroll(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->enrolledCourses()->where('courses.id', $course->id)->exists()) {
            return back()->with('error', 'Ya estás inscrito en este curso.');
        }

        if ($course->user_id === $user->id) {
            return back()->with('error', 'No puedes inscribirte a tu propio curso.');
        }

        $user->enrolledCourses()->attach($course->id);

        return back()->with('success', '¡Inscripción exitosa! Ahora puedes ver el curso en la pestaña "Mis Cursos".');
    }

   
    public function toggleLive(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para iniciar esta clase.');
        }

        $course->is_live_now = !$course->is_live_now;
        $course->save();

        $status = $course->is_live_now ? 'iniciada' : 'finalizada';
        return back()->with('success', "La clase en vivo ha sido $status correctamente.");
    }
}
