<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\ContentCalendarController;
use App\Http\Controllers\Admin\ContentItemController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DemoRequestController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\HeroSettingsController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\JobApplicationController as AdminJobApplicationController;
use App\Http\Controllers\Admin\MarketingTaskController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PulseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SearchController as AdminSearchController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SocialAccountController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TrackedLinkController;
use App\Models\BlogPost;
use App\Models\Position;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'));
Route::get('/about', fn () => view('about'));
Route::get('/services', fn () => view('services'));

Route::get('/products', function () {
    try {
        $products = Product::query()->active()->ordered()->get();
        if ($products->isEmpty()) {
            $products = collect(config('portfolio.products', []))->map(fn ($p) => (object) $p);
            $dbProducts = collect();
        } else {
            $dbProducts = collect();
        }
    } catch (\Throwable $e) {
        $products = collect(config('portfolio.products', []))->map(fn ($p) => (object) $p);
        $dbProducts = collect();
    }

    return view('products', compact('products', 'dbProducts'));
});

Route::get('/careers', function () {
    $positions = Position::where('is_active', true)->ordered()->get();

    return view('careers', compact('positions'));
})->name('careers.index');

Route::get('/careers/{position}/apply', [JobApplicationController::class, 'create'])->name('careers.apply');
Route::post('/careers/apply', [JobApplicationController::class, 'store'])->name('careers.apply.store');

Route::get('/contact', function () {
    $products = Product::query()->active()->ordered()->get(['id', 'name', 'slug']);

    return view('contact', compact('products'));
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
    if (! $post->is_published) {
        abort(404);
    }

    $relatedPosts = BlogPost::where('is_published', true)
        ->where('id', '!=', $post->id)
        ->when($post->category, fn ($q) => $q->where('category', $post->category))
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();

    return view('blog.show', compact('post', 'relatedPosts'));
})->name('blog.show');

Route::get('/policies', fn () => view('policies'));
Route::get('/faq', fn () => view('faq'));
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/t/{code}', [TrackedLinkController::class, 'show'])->name('tracked.show');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('permission:products.view|products.manage')->prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->middleware('permission:products.manage')->name('create');
        Route::post('/', [ProductController::class, 'store'])->middleware('permission:products.manage')->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:products.manage')->name('edit');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::post('/{product}', [ProductController::class, 'update'])->middleware('permission:products.manage')->name('update');
        Route::post('/{product}/delete', [ProductController::class, 'destroy'])->middleware('permission:products.manage')->name('destroy');
        Route::post('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->middleware('permission:products.manage')->name('toggle-status');
    });

    Route::middleware('permission:leads.view')->prefix('enquiries')->name('enquiries.')->group(function () {
        Route::get('/', [EnquiryController::class, 'index'])->name('index');
        Route::get('/{enquiry}', [EnquiryController::class, 'show'])->name('show');
        Route::post('/{enquiry}/status', [EnquiryController::class, 'updateStatus'])->middleware('permission:leads.manage')->name('updateStatus');
        Route::post('/{enquiry}/reply', [EnquiryController::class, 'reply'])->middleware('permission:leads.manage')->name('reply');
        Route::post('/{enquiry}/delete', [EnquiryController::class, 'destroy'])->middleware('permission:leads.manage')->name('destroy');
    });

    Route::middleware('permission:users.view|users.manage')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->middleware('permission:users.manage')->name('create');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.manage')->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.manage')->name('edit');
        Route::post('/{user}', [UserController::class, 'update'])->middleware('permission:users.manage')->name('update');
        Route::post('/{user}/delete', [UserController::class, 'destroy'])->middleware('permission:users.manage')->name('destroy');
    });

    Route::middleware('permission:roles.view|roles.manage')->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.manage')->name('edit');
        Route::post('/{role}', [RoleController::class, 'update'])->middleware('permission:roles.manage')->name('update');
    });

    Route::get('/search', [AdminSearchController::class, 'index'])->name('search');
    Route::get('/search/api', [AdminSearchController::class, 'api'])->name('search.api');

    Route::middleware('permission:blog.view|blog.manage')->prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/create', [BlogController::class, 'create'])->middleware('permission:blog.manage')->name('create');
        Route::post('/', [BlogController::class, 'store'])->middleware('permission:blog.manage')->name('store');
        Route::get('/{post}/edit', [BlogController::class, 'edit'])->middleware('permission:blog.manage')->name('edit');
        Route::post('/{post}', [BlogController::class, 'update'])->middleware('permission:blog.manage')->name('update');
        Route::post('/{post}/delete', [BlogController::class, 'destroy'])->middleware('permission:blog.manage')->name('destroy');
    });

    Route::middleware('permission:careers.view|careers.manage')->prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('index');
        Route::get('/create', [PositionController::class, 'create'])->middleware('permission:careers.manage')->name('create');
        Route::post('/', [PositionController::class, 'store'])->middleware('permission:careers.manage')->name('store');
        Route::get('/{position}', [PositionController::class, 'show'])->name('show');
        Route::get('/{position}/edit', [PositionController::class, 'edit'])->middleware('permission:careers.manage')->name('edit');
        Route::post('/{position}', [PositionController::class, 'update'])->middleware('permission:careers.manage')->name('update');
        Route::post('/{position}/delete', [PositionController::class, 'destroy'])->middleware('permission:careers.manage')->name('destroy');
    });

    Route::middleware('permission:careers.view|careers.manage')->prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [AdminJobApplicationController::class, 'index'])->name('index');
        Route::post('/bulk-action', [AdminJobApplicationController::class, 'bulkAction'])->middleware('permission:careers.manage')->name('bulkAction');
        Route::get('/{application}', [AdminJobApplicationController::class, 'show'])->name('show');
        Route::post('/{application}/status', [AdminJobApplicationController::class, 'updateStatus'])->middleware('permission:careers.manage')->name('updateStatus');
        Route::post('/{application}/schedule-interview', [AdminJobApplicationController::class, 'scheduleInterview'])->middleware('permission:careers.manage')->name('scheduleInterview');
        Route::post('/{application}/interview-notes', [AdminJobApplicationController::class, 'updateInterviewNotes'])->middleware('permission:careers.manage')->name('updateInterviewNotes');
        Route::post('/{application}/send-message', [AdminJobApplicationController::class, 'sendMessage'])->middleware('permission:careers.manage')->name('sendMessage');
        Route::post('/{application}/comments', [AdminJobApplicationController::class, 'addComment'])->middleware('permission:careers.manage')->name('addComment');
        Route::post('/comments/{comment}/delete', [AdminJobApplicationController::class, 'deleteComment'])->middleware('permission:careers.manage')->name('deleteComment');
        Route::get('/{application}/resume', [AdminJobApplicationController::class, 'downloadResume'])->name('downloadResume');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
    });

    Route::middleware('permission:settings.view|settings.manage')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::post('/general', [SettingsController::class, 'updateGeneral'])->middleware('permission:settings.manage')->name('general.update');
        Route::get('/hero', [HeroSettingsController::class, 'edit'])->name('hero.edit');
        Route::post('/hero', [HeroSettingsController::class, 'update'])->middleware('permission:settings.manage')->name('hero.update');
    });

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware('permission:audit.view')->name('activity-logs.index');

    // Marketing modules
    Route::middleware('permission:campaigns.view')->prefix('campaigns')->name('campaigns.')->group(function () {
        Route::get('/', [CampaignController::class, 'index'])->name('index');
        Route::get('/create', [CampaignController::class, 'create'])->middleware('permission:campaigns.create|campaigns.manage')->name('create');
        Route::post('/', [CampaignController::class, 'store'])->middleware('permission:campaigns.create|campaigns.manage')->name('store');
        Route::get('/{campaign}', [CampaignController::class, 'show'])->name('show');
        Route::get('/{campaign}/edit', [CampaignController::class, 'edit'])->middleware('permission:campaigns.edit|campaigns.manage')->name('edit');
        Route::post('/{campaign}', [CampaignController::class, 'update'])->middleware('permission:campaigns.edit|campaigns.manage')->name('update');
        Route::post('/{campaign}/delete', [CampaignController::class, 'destroy'])->middleware('permission:campaigns.manage')->name('destroy');
    });

    Route::middleware('permission:content.view')->prefix('content')->name('content.')->group(function () {
        Route::get('/library', [ContentItemController::class, 'library'])->name('library');
        Route::get('/calendar', [ContentCalendarController::class, 'index'])->name('calendar');
        Route::get('/approvals', [ContentItemController::class, 'approvals'])->name('approvals');
        Route::get('/create', [ContentItemController::class, 'create'])->middleware('permission:content.create')->name('create');
        Route::post('/', [ContentItemController::class, 'store'])->middleware('permission:content.create')->name('store');
        Route::get('/{content}/edit', [ContentItemController::class, 'edit'])->middleware('permission:content.edit')->name('edit');
        Route::post('/{content}', [ContentItemController::class, 'update'])->middleware('permission:content.edit')->name('update');
        Route::post('/{content}/delete', [ContentItemController::class, 'destroy'])->middleware('permission:content.delete')->name('destroy');
        Route::post('/{content}/submit', [ContentItemController::class, 'submitForReview'])->middleware('permission:content.edit')->name('submit');
        Route::post('/{content}/approve', [ContentItemController::class, 'approve'])->middleware('permission:content.approve')->name('approve');
        Route::post('/{content}/schedule', [ContentItemController::class, 'schedule'])->middleware('permission:content.publish')->name('schedule');
        Route::post('/{content}/publish', [ContentItemController::class, 'publish'])->middleware('permission:content.publish')->name('publish');
    });

    Route::middleware('permission:social_accounts.view')->prefix('social-accounts')->name('social-accounts.')->group(function () {
        Route::get('/', [SocialAccountController::class, 'index'])->name('index');
        Route::get('/create', [SocialAccountController::class, 'create'])->middleware('permission:social_accounts.manage')->name('create');
        Route::post('/', [SocialAccountController::class, 'store'])->middleware('permission:social_accounts.manage')->name('store');
        Route::get('/{socialAccount}', [SocialAccountController::class, 'show'])->name('show');
        Route::get('/{socialAccount}/edit', [SocialAccountController::class, 'edit'])->middleware('permission:social_accounts.manage')->name('edit');
        Route::post('/{socialAccount}', [SocialAccountController::class, 'update'])->middleware('permission:social_accounts.manage')->name('update');
        Route::post('/{socialAccount}/delete', [SocialAccountController::class, 'destroy'])->middleware('permission:social_accounts.manage')->name('destroy');
    });

    Route::middleware('permission:integrations.view')->prefix('integrations')->name('integrations.')->group(function () {
        Route::get('/', [IntegrationController::class, 'index'])->name('index');
        Route::post('/', [IntegrationController::class, 'store'])->middleware('permission:integrations.manage')->name('store');
        Route::post('/{integration}/reconnect', [IntegrationController::class, 'reconnect'])->middleware('permission:integrations.manage')->name('reconnect');
        Route::post('/{integration}/delete', [IntegrationController::class, 'destroy'])->middleware('permission:integrations.manage')->name('destroy');
    });

    Route::middleware('permission:analytics.view')->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'overview'])->name('overview');
        Route::get('/social', [AnalyticsController::class, 'social'])->name('social');
        Route::get('/website', [AnalyticsController::class, 'website'])->name('website');
        Route::get('/campaigns', [AnalyticsController::class, 'campaigns'])->name('campaigns');
        Route::get('/products', [AnalyticsController::class, 'products'])->name('products');
        Route::get('/content', [AnalyticsController::class, 'content'])->name('content');
    });

    Route::middleware('permission:demo_requests.view')->prefix('demos')->name('demos.')->group(function () {
        Route::get('/', [DemoRequestController::class, 'index'])->name('index');
        Route::get('/{demo}', [DemoRequestController::class, 'show'])->name('show');
        Route::post('/{demo}', [DemoRequestController::class, 'update'])->middleware('permission:demo_requests.manage')->name('update');
    });

    Route::middleware('permission:pulse.view')->prefix('pulse')->name('pulse.')->group(function () {
        Route::get('/', [PulseController::class, 'index'])->name('index');
        Route::post('/{alert}/resolve', [PulseController::class, 'resolve'])->name('resolve');
    });

    Route::middleware('permission:tasks.view')->prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [MarketingTaskController::class, 'index'])->name('index');
        Route::post('/', [MarketingTaskController::class, 'store'])->middleware('permission:tasks.manage')->name('store');
        Route::post('/{task}', [MarketingTaskController::class, 'update'])->middleware('permission:tasks.manage')->name('update');
        Route::post('/{task}/delete', [MarketingTaskController::class, 'destroy'])->middleware('permission:tasks.manage')->name('destroy');
    });

    Route::get('/subscribers', [SubscriberController::class, 'index'])
        ->middleware('permission:subscribers.view')
        ->name('subscribers.index');
});
