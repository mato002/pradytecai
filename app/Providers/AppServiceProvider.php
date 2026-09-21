<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\ContactMessage;
use App\Models\ContentItem;
use App\Models\JobApplication;
use App\Models\Position;
use App\Models\Product;
use App\Models\SocialAccount;
use App\Models\User;
use App\Policies\CampaignPolicy;
use App\Policies\ContactMessagePolicy;
use App\Policies\ContentItemPolicy;
use App\Policies\JobApplicationPolicy;
use App\Policies\PositionPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SocialAccountPolicy;
use App\Services\Social\BufferPublisher;
use App\Services\Social\SocialPublisher;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        Product::class => ProductPolicy::class,
        Position::class => PositionPolicy::class,
        JobApplication::class => JobApplicationPolicy::class,
        ContactMessage::class => ContactMessagePolicy::class,
        Campaign::class => CampaignPolicy::class,
        ContentItem::class => ContentItemPolicy::class,
        SocialAccount::class => SocialAccountPolicy::class,
    ];

    public function register(): void
    {
        $this->app->bind(SocialPublisher::class, BufferPublisher::class);
    }

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return null;
        });

        View::composer('layouts.admin', function ($view) {
            if (! auth()->check()) {
                return;
            }

            $products = Product::query()
                ->active()
                ->ordered()
                ->get(['id', 'name', 'slug']);

            $user = auth()->user();
            $scopedIds = $user->scopedProductIds();
            if ($scopedIds !== null) {
                $products = $products->whereIn('id', $scopedIds)->values();
            }

            $view->with([
                'filterProducts' => $products,
                'activeProductFilter' => session('marketing.product_id'),
            ]);
        });
    }
}
