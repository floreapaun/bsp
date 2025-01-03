<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConversationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Posts');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin-dashboard', function () {
    return Inertia::render('AdminDashboard');
})->middleware(['auth', 'verified'])->name('admin-dashboard')->middleware(CheckAdmin::class);

Route::get('/add-post', function () {
    return Inertia::render('AddPost');
})->middleware(['auth', 'verified'])->name('add-post');

Route::get('/my-posts', function () {
    return Inertia::render('MyPosts');
})->middleware(['auth', 'verified'])->name('my-posts');

Route::get('/messenger', function () {
    return Inertia::render('Messenger');
})->middleware(['auth', 'verified'])->name('messenger');

Route::middleware('auth')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json(Auth::user());
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/all', [PostController::class, 'indexAll'])->name('posts.indexAll');
    Route::get('/posts/user/', [PostController::class, 'indexUser'])->name('posts.user');
    Route::get('/posts/search/', [PostController::class, 'search']);
    Route::get('/posts/search/user/', [PostController::class, 'searchUser']);
    Route::post('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/images/{id}', [ImageController::class, 'destroy']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::patch('/posts/{id}', [PostController::class, 'updateActive'])->middleware(CheckAdmin::class);
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/conversations/{id}', [MessageController::class, 'show']);
    Route::get('/auth_conversations/', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/{conversationId}', [MessageController::class, 'index']);
    Route::get('/last_message/{conversationId}', [MessageController::class, 'lastMessage']);
});

require __DIR__.'/auth.php';
