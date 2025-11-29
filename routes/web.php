<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GroupController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (Invitados)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Usuarios Autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // --- INICIO ---
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // --- PUBLICACIONES (POSTS) ---
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/{id}/like', [PostController::class, 'like'])->name('posts.like');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

    // --- COMENTARIOS ---
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // --- PERFIL ---
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // --- AMIGOS ---
    Route::prefix('friends')->group(function () {
        Route::get('/', [FriendController::class, 'index'])->name('friends');
        Route::get('/search', [FriendController::class, 'search'])->name('friends.search');
        Route::post('/send-request/{user}', [FriendController::class, 'sendRequest'])->name('friends.send');
        Route::post('/accept-request/{user}', [FriendController::class, 'acceptRequest'])->name('friends.accept');
        Route::post('/reject-request/{user}', [FriendController::class, 'rejectRequest'])->name('friends.reject');
        Route::post('/remove-friend/{user}', [FriendController::class, 'removeFriend'])->name('friends.remove');
    });

    // --- GRUPOS (Lógica Completa) ---
    Route::prefix('groups')->group(function () {
        // 1. Listado y Creación
        Route::get('/', [GroupController::class, 'index'])->name('groups');
        Route::post('/', [GroupController::class, 'store'])->name('groups.store');

        // 2. Acciones de Miembros (Unirse/Salir)
        // Nota: Es importante definir estas rutas ANTES de la ruta wildcard /{group} si hubiera conflicto, 
        // pero como tienen prefijos /join y /leave, están seguras aquí.
        Route::post('/{group}/join', [GroupController::class, 'join'])->name('groups.join');
        Route::post('/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');

        // 3. Ver Grupo Individual y Actualizar (Editar)
        Route::get('/{group}', [GroupController::class, 'show'])->name('groups.show');
        Route::put('/{group}', [GroupController::class, 'update'])->name('groups.update'); // <--- Para editar foto/nombre

        // 4. Mensajes del Grupo
        Route::post('/{group}/messages', [GroupController::class, 'storeMessage'])->name('groups.message');
        
        // 5. Moderación (Eliminar mensaje y Expulsar)
        // Usamos {message} y {user} para que coincidan con los argumentos del controlador
        Route::delete('/{group}/message/{message}', [GroupController::class, 'deleteMessage'])->name('groups.deleteMessage');
        Route::post('/{group}/expel/{user}', [GroupController::class, 'expelMember'])->name('groups.expelMember');
    });

    // --- CURSOS ---
    // Nota: Si creaste un CourseController, deberías cambiar HomeController por CourseController aquí.
    Route::get('/courses', [HomeController::class, 'courses'])->name('courses');
    Route::post('/courses', [HomeController::class, 'storeCourse'])->name('courses.store');
    Route::post('/courses/{course}/enroll', [HomeController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/toggle-live', [HomeController::class, 'toggleLive'])->name('courses.toggle-live');

    // --- OTRAS SECCIONES ---
    Route::get('/events', [HomeController::class, 'events'])->name('events');
    Route::get('/resources', [HomeController::class, 'resources'])->name('resources');

    // --- AULA VIRTUAL ---
    Route::get('/classroom/{id}', function ($id) {
        return view('virtual-classroom', ['id' => $id]);
    })->name('classroom');

});