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
use App\Http\Controllers\TestController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ShopController; 

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
| RUTAS DE DIAGNÓSTICO (ACCESO PÚBLICO TEMPORAL)
|--------------------------------------------------------------------------
*/

// 1. Ver qué modelos de IA tienes disponibles
Route::get('/debug-models', function () {
    $apiKey = env('GEMINI_API_KEY');
    
    try {
        $response = Illuminate\Support\Facades\Http::withoutVerifying()
            ->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");

        if ($response->failed()) {
            return [
                "estado" => "❌ ERROR DE CONEXIÓN",
                "error" => $response->json()
            ];
        }

        $models = $response->json()['models'] ?? [];
        $generateModels = array_filter($models, function($m) {
            return in_array('generateContent', $m['supportedGenerationMethods']);
        });

        return [
            "estado" => "✅ CONEXIÓN EXITOSA",
            "mensaje" => "Copia uno de estos nombres en tu GeminiService.php:",
            "modelos_disponibles" => array_values(array_column($generateModels, 'name'))
        ];
    } catch (\Exception $e) {
        return ["error" => $e->getMessage()];
    }
});

// 2. Ver configuración de PHP
Route::get('/info-php', function () {
    phpinfo();
});

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Usuarios Autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // --- INICIO ---
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // --- PUBLICACIONES ---
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

    // --- CHAT ---
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/private', [ChatController::class, 'storePrivate'])->name('private');
        Route::post('/group', [ChatController::class, 'storeGroup'])->name('group');
        Route::get('/{id}', [ChatController::class, 'show'])->name('show');
        Route::post('/{id}/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/{id}/leave', [ChatController::class, 'leaveGroup'])->name('leave');
        Route::delete('/{id}/delete', [ChatController::class, 'deleteGroup'])->name('delete');
        Route::post('/{id}/photo', [ChatController::class, 'updatePhoto'])->name('photo');
        Route::post('/{id}/add-members', [ChatController::class, 'addMembers'])->name('addMembers');
        Route::post('/{id}/make-admin/{user}', [ChatController::class, 'makeAdmin'])->name('makeAdmin');
        Route::post('/{id}/remove-member/{user}', [ChatController::class, 'removeMember'])->name('removeMember');
    });

    // --- GRUPOS DE ESTUDIO ---
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

    // --- IA / EXÁMENES ---
    Route::resource('tests', TestController::class);
    Route::post('/tests/{test}/submit', [TestController::class, 'submit'])->name('tests.submit');

    // --- AULA VIRTUAL ---
    Route::get('/classroom/{id}', function ($id) {
        $course = \App\Models\Course::findOrFail($id);
        return view('virtual-classroom', compact('course'));
    })->name('classroom');

    // --- GAMIFICACIÓN / MASCOTA ---
    Route::post('/pet/select', [PetController::class, 'select'])->name('pet.select');
    Route::post('/pet/rename', [PetController::class, 'rename'])->name('pet.rename'); // <--- ESTA FALTABA

    // --- TIENDA DE MASCOTAS E INVENTARIO ---
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::post('/shop/buy/{id}', [ShopController::class, 'buy'])->name('shop.buy');
    Route::get('/shop/inventory', [ShopController::class, 'inventory'])->name('shop.inventory');
    Route::post('/shop/equip/{id}', [ShopController::class, 'toggleEquip'])->name('shop.equip');

});