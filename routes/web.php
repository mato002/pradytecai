<?php

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\HeroSettingsController;
use App\Http\Controllers\Admin\JobApplicationController as AdminJobApplicationController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SearchController as AdminSearchController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\SearchController;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $heroImagePath = \App\Models\SiteSetting::get('hero_background_image');
    $heroImageUrl = $heroImagePath ? asset('storage/' . ltrim($heroImagePath, '/')) : null;

    return view('home', compact('heroImageUrl'));
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
    $positions = Position::where('is_active', true)
        ->ordered()
        ->get();

    return view('careers', compact('positions'));
})->name('careers.index');

// Job Application Routes
Route::get('/careers/{position}/apply', [JobApplicationController::class, 'create'])->name('careers.apply');
Route::post('/careers/apply', [JobApplicationController::class, 'store'])->name('careers.apply.store');

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

Route::get('/blog/{post:slug}', function (BlogPost $post) {
    if (!$post->is_published) {
        abort(404);
    }
    
    // Get related posts (same category, excluding current)
    $relatedPosts = BlogPost::where('is_published', true)
        ->where('id', '!=', $post->id)
        ->when($post->category, function ($query) use ($post) {
            return $query->where('category', $post->category);
        })
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();
    
    return view('blog.show', compact('post', 'relatedPosts'));
})->name('blog.show');

Route::get('/policies', function () {
    return view('policies');
});

Route::get('/faq', function () {
    return view('faq');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

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
        $stats = [
            'total_users' => User::count(),
            'active_products' => 0, // Placeholder - no Product model yet
            'new_enquiries' => \App\Models\ContactMessage::where('created_at', '>=', now()->subDays(7))->count(),
            'published_posts' => \App\Models\BlogPost::where('is_published', true)->count(),
            'active_positions' => \App\Models\Position::where('is_active', true)->count(),
        ];
        
        $recent_enquiries = \App\Models\ContactMessage::latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recent_enquiries'));
    })->name('dashboard');

    // Products management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::post('/{product}', [ProductController::class, 'update'])->name('update');
        Route::post('/{product}/delete', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
    });

    Route::get('/enquiries', function () {
        $messages = ContactMessage::latest()->paginate(20);

        return view('admin.enquiries.index', compact('messages'));
    })->name('enquiries.index');

    // Users management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::post('/{user}', [UserController::class, 'update'])->name('update');
        Route::post('/{user}/delete', [UserController::class, 'destroy'])->name('destroy');
    });

    // Admin search
    Route::get('/search', [AdminSearchController::class, 'index'])->name('search');
    Route::get('/search/api', [AdminSearchController::class, 'api'])->name('search.api');

    // Blog management
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/create', [BlogController::class, 'create'])->name('create');
        Route::post('/', [BlogController::class, 'store'])->name('store');
        Route::get('/{post}/edit', [BlogController::class, 'edit'])->name('edit');
        Route::post('/{post}', [BlogController::class, 'update'])->name('update');
        Route::post('/{post}/delete', [BlogController::class, 'destroy'])->name('destroy');
    });

    // Positions management
    Route::prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('index');
        Route::get('/create', [PositionController::class, 'create'])->name('create');
        Route::post('/', [PositionController::class, 'store'])->name('store');
        Route::get('/{position}/edit', [PositionController::class, 'edit'])->name('edit');
        Route::post('/{position}', [PositionController::class, 'update'])->name('update');
        Route::post('/{position}/delete', [PositionController::class, 'destroy'])->name('destroy');
    });

    // Job Applications management
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [AdminJobApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [AdminJobApplicationController::class, 'show'])->name('show');
        Route::post('/{application}/status', [AdminJobApplicationController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{application}/schedule-interview', [AdminJobApplicationController::class, 'scheduleInterview'])->name('scheduleInterview');
        Route::post('/{application}/interview-notes', [AdminJobApplicationController::class, 'updateInterviewNotes'])->name('updateInterviewNotes');
        Route::post('/{application}/send-message', [AdminJobApplicationController::class, 'sendMessage'])->name('sendMessage');
        Route::get('/{application}/resume', [AdminJobApplicationController::class, 'downloadResume'])->name('downloadResume');
    });

    // Profile management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::post('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');
        Route::get('/hero', [HeroSettingsController::class, 'edit'])->name('hero.edit');
        Route::post('/hero', [HeroSettingsController::class, 'update'])->name('hero.update');
    });
});



