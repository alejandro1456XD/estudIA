<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ChatController;

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

    // --- CHAT Y GESTIÓN DE GRUPOS (ACTUALIZADO) ---
    Route::prefix('chat')->name('chat.')->group(function () {
        // Rutas básicas
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/private', [ChatController::class, 'storePrivate'])->name('private');
        Route::post('/group', [ChatController::class, 'storeGroup'])->name('group');
        Route::get('/{id}', [ChatController::class, 'show'])->name('show');
        Route::post('/{id}/send', [ChatController::class, 'sendMessage'])->name('send');

        // NUEVAS RUTAS DE GESTIÓN
        Route::post('/{id}/leave', [ChatController::class, 'leaveGroup'])->name('leave');
        Route::delete('/{id}/delete', [ChatController::class, 'deleteGroup'])->name('delete');
        Route::post('/{id}/photo', [ChatController::class, 'updatePhoto'])->name('photo');
        Route::post('/{id}/add-members', [ChatController::class, 'addMembers'])->name('addMembers');
        Route::post('/{id}/make-admin/{user}', [ChatController::class, 'makeAdmin'])->name('makeAdmin');
        Route::post('/{id}/remove-member/{user}', [ChatController::class, 'removeMember'])->name('removeMember');
    });

    // --- GRUPOS (ESTUDIO) ---
    Route::prefix('groups')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('groups');
        Route::post('/', [GroupController::class, 'store'])->name('groups.store');
        Route::post('/{group}/join', [GroupController::class, 'join'])->name('groups.join');
        Route::post('/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
        Route::get('/{group}', [GroupController::class, 'show'])->name('groups.show');
        Route::put('/{group}', [GroupController::class, 'update'])->name('groups.update'); 
        Route::post('/{group}/messages', [GroupController::class, 'storeMessage'])->name('groups.message');
        Route::delete('/{group}/message/{message}', [GroupController::class, 'deleteMessage'])->name('groups.deleteMessage');
        Route::post('/{group}/expel/{user}', [GroupController::class, 'expelMember'])->name('groups.expelMember');
    });

    // --- CURSOS ---
    Route::get('/courses', [HomeController::class, 'courses'])->name('courses');
    Route::post('/courses', [HomeController::class, 'storeCourse'])->name('courses.store');
    Route::post('/courses/{course}/enroll', [HomeController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/toggle-live', [HomeController::class, 'toggleLive'])->name('courses.toggle-live');

    // --- RECURSOS ---
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources');
    Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    Route::post('/resources/{resource}/rate', [ResourceController::class, 'rate'])->name('resources.rate');

    // --- EVENTOS ---
    Route::get('/events', [EventController::class, 'index'])->name('events');
    Route::get('/events/my', [EventController::class, 'myEvents'])->name('events.my');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::post('/events/{event}/attend', [EventController::class, 'toggleAttendance'])->name('events.attend');

    // --- AULA VIRTUAL ---
    Route::get('/classroom/{id}', function ($id) {
        return view('virtual-classroom', ['id' => $id]);
    })->name('classroom');

});