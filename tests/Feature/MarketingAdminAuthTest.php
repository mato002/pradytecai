<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MarketingAdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_super_admin_can_open_products(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk();
    }

    public function test_hr_manager_cannot_open_products(): void
    {
        $hr = User::factory()->hrManager()->create();
        $hr->assignRole('hr_manager');

        $this->actingAs($hr)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_hr_manager_can_open_positions(): void
    {
        $hr = User::factory()->hrManager()->create();
        $hr->assignRole('hr_manager');

        $this->actingAs($hr)
            ->get(route('admin.positions.index'))
            ->assertOk();
    }

    public function test_unscoped_marketing_analyst_redirected_from_dashboard_without_permission_leak_on_careers_only_user(): void
    {
        $user = User::factory()->create(['role' => 'marketing_analyst']);
        // No Spatie role attached → no permissions
        $this->actingAs($user)
            ->get(route('admin.positions.index'))
            ->assertForbidden();
    }

    public function test_scoped_user_cannot_view_other_product(): void
    {
        $user = User::factory()->create(['role' => 'marketing_analyst']);
        $user->assignRole('marketing_analyst');

        $allowed = Product::create([
            'name' => 'Allowed Product',
            'slug' => 'allowed-product',
            'is_active' => true,
            'order' => 1,
        ]);
        $denied = Product::create([
            'name' => 'Denied Product',
            'slug' => 'denied-product',
            'is_active' => true,
            'order' => 2,
        ]);

        $user->accessScopes()->create([
            'scope_type' => 'product',
            'scope_id' => $allowed->id,
        ]);

        $this->actingAs($user)
            ->get(route('admin.products.show', $denied))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.products.show', $allowed))
            ->assertOk();
    }

    public function test_contact_stores_product_and_utm(): void
    {
        $product = Product::create([
            'name' => 'Prady Microfinance',
            'slug' => 'prady-microfinance',
            'is_active' => true,
            'order' => 1,
        ]);

        $this->post(route('contact.store'), [
            'name' => 'Test Lead',
            'email' => 'lead@example.com',
            'subject' => 'Demo: prady-microfinance',
            'message' => 'Interested in a demo',
            'topic' => 'prady-microfinance',
            'request_type' => 'demo',
            'utm_source' => 'facebook',
            'utm_campaign' => 'spring',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'lead@example.com',
            'product_id' => $product->id,
            'utm_source' => 'facebook',
            'utm_campaign' => 'spring',
            'request_type' => 'demo',
        ]);

        $this->assertDatabaseHas('demo_requests', [
            'product_id' => $product->id,
            'status' => 'requested',
        ]);
    }

    public function test_public_products_page_loads(): void
    {
        Product::create([
            'name' => 'Prady Microfinance',
            'slug' => 'prady-microfinance',
            'is_active' => true,
            'order' => 1,
            'description' => 'MFI platform',
        ]);

        $this->get('/products')->assertOk()->assertSee('prady-microfinance', false);
    }

    public function test_newsletter_persists_subscriber(): void
    {
        $this->post(route('newsletter.subscribe'), [
            'email' => 'news@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'news@example.com',
            'status' => 'subscribed',
        ]);
    }
}
