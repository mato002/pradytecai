<?php

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/services', function () {
    return view('services');
});

Route::get('/products', function () {
    return view('products');
});

Route::get('/careers', function () {
    return view('careers');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/blog', function () {
    $posts = BlogPost::where('is_published', true)
        ->orderByDesc('published_at')
        ->orderByDesc('created_at')
        ->get();

    return view('blog', compact('posts'));
})->name('blog.index');

Route::get('/policies', function () {
    return view('policies');
});

Route::post('/newsletter/subscribe', function () {
    // TODO: Implement newsletter subscription logic
    return redirect()->back()->with('newsletter_success', 'Thank you for subscribing!');
})->name('newsletter.subscribe');

// Authentication Routes (Admin Only)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin area (protected by authentication)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/products', function () {
        return view('admin.products.index');
    })->name('products.index');

    Route::get('/enquiries', function () {
        $messages = ContactMessage::latest()->paginate(20);

        return view('admin.enquiries.index', compact('messages'));
    })->name('enquiries.index');

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    // Blog management
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/create', [BlogController::class, 'create'])->name('create');
        Route::post('/', [BlogController::class, 'store'])->name('store');
        Route::get('/{post}/edit', [BlogController::class, 'edit'])->name('edit');
        Route::post('/{post}', [BlogController::class, 'update'])->name('update');
        Route::post('/{post}/delete', [BlogController::class, 'destroy'])->name('destroy');
    });
});



