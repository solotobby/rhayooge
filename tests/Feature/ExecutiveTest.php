<?php

namespace Tests\Feature;

use App\Livewire\Admin\Executives;
use App\Livewire\Admin\ProductForm;
use App\Livewire\Executive\Portal as ExecutivePortal;
use App\Livewire\Store\Checkout;
use App\Mail\ExecutiveInviteMail;
use App\Models\BeCommission;
use App\Models\BusinessExecutive;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ExecutiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_executives_desk(): void
    {
        $this->get('/admin/executives')->assertRedirect('/admin/login');

        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin/executives')->assertForbidden();
    }

    public function test_admin_can_view_executives_desk(): void
    {
        $admin = User::factory()->admin()->create();
        BusinessExecutive::factory()->create([
            'name' => 'Kemi Adebayo',
            'code' => 'kemi24',
        ]);

        $this->actingAs($admin)
            ->get('/admin/executives')
            ->assertOk()
            ->assertSee('Business Executives')
            ->assertSee('Kemi Adebayo')
            ->assertSee('kemi24');
    }

    public function test_admin_can_open_and_close_create_modal(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Executives::class)
            ->assertSet('showCreateModal', false)
            ->call('openCreate')
            ->assertSet('showCreateModal', true)
            ->assertSee('New Business Executive')
            ->assertSee('Full Name')
            ->assertSee('Email Address')
            ->assertSee('Phone Number')
            ->assertDontSee('Default Commission Rate')
            ->call('closeModals')
            ->assertSet('showCreateModal', false);
    }

    public function test_admin_can_create_business_executive_and_email_is_sent(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Executives::class)
            ->set('name', 'Chioma Okafor')
            ->set('email', 'chioma@example.com')
            ->set('phone', '+2348012345678')
            ->call('saveExecutive')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('business_executives', [
            'name' => 'Chioma Okafor',
            'email' => 'chioma@example.com',
            'phone' => '+2348012345678',
            'default_commission_rate' => 10,
            'status' => 'active',
        ]);

        $executive = BusinessExecutive::where('email', 'chioma@example.com')->firstOrFail();
        $this->assertNotEmpty($executive->code);
        $this->assertNotEmpty($executive->invite_token);

        Mail::assertSent(ExecutiveInviteMail::class, function ($mail) use ($executive) {
            return $mail->hasTo('chioma@example.com') && $mail->executive->id === $executive->id;
        });
    }

    public function test_admin_can_set_custom_commission_on_product(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProductForm::class)
            ->set('name', 'Silk Boubou Luxe')
            ->set('slug', 'silk-boubou-luxe')
            ->set('category', 'Dresses')
            ->set('price', '80000')
            ->set('commission_type', 'percent')
            ->set('commission_rate', 15)
            ->set('description', 'Flowing raw silk boubou in sunset gold with embroidered neckline.')
            ->set('image', 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446')
            ->set('selectedSizes', ['S', 'M', 'L'])
            ->call('save')
            ->assertRedirect(route('admin.products'));

        $this->assertDatabaseHas('products', [
            'slug' => 'silk-boubou-luxe',
            'price' => 80000,
            'commission_type' => 'percent',
            'commission_rate' => 15,
        ]);

        $product = Product::where('slug', 'silk-boubou-luxe')->firstOrFail();
        $this->assertSame(12000, $product->calculateCommissionAmount());
    }

    public function test_executive_portal_loads_and_displays_product_links(): void
    {
        $executive = BusinessExecutive::factory()->create([
            'name' => 'Zainab Balogun',
            'code' => 'zainab',
            'invite_token' => 'test-valid-token-12345',
            'status' => 'active',
        ]);

        $product = Product::factory()->create([
            'name' => 'Emerald Linen Kimono',
            'slug' => 'emerald-linen-kimono',
            'price' => 60000,
            'commission_type' => 'fixed',
            'commission_rate' => 5000,
        ]);

        $this->get('/executive/portal/' . $executive->invite_token)
            ->assertOk()
            ->assertSee('Zainab Balogun')
            ->assertSee('zainab')
            ->assertSee('Emerald Linen Kimono')
            ->assertSee('ref=zainab');
    }

    public function test_executive_can_update_bank_details_in_portal(): void
    {
        $executive = BusinessExecutive::factory()->create([
            'invite_token' => 'bank-test-token',
        ]);

        Livewire::test(ExecutivePortal::class, ['token' => $executive->invite_token])
            ->set('bank_name', 'Guaranty Trust Bank')
            ->set('bank_account_number', '0123456789')
            ->set('bank_account_name', 'Zainab Balogun')
            ->call('saveBankDetails')
            ->assertHasNoErrors()
            ->assertSee('Banking details saved for commission payouts.');

        $this->assertDatabaseHas('business_executives', [
            'id' => $executive->id,
            'bank_name' => 'Guaranty Trust Bank',
            'bank_account_number' => '0123456789',
            'bank_account_name' => 'Zainab Balogun',
        ]);
    }

    public function test_referral_cookie_and_session_are_tracked_and_awarded_on_checkout(): void
    {
        $executive = BusinessExecutive::factory()->create([
            'name' => 'Funmi Williams',
            'code' => 'funmi10',
            'default_commission_rate' => 10,
        ]);

        $product1 = Product::factory()->create([
            'name' => 'Royal Adire Kimono',
            'slug' => 'royal-adire-kimono',
            'price' => 50000,
            'commission_type' => 'percentage',
            'commission_rate' => 10,
            'quantity' => 10,
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Silk Velvet Turban',
            'slug' => 'silk-velvet-turban',
            'price' => 20000,
            'commission_type' => 'fixed',
            'commission_rate' => 3000,
            'quantity' => 5,
        ]);

        // Customer visits shop with referral code
        $this->get('/shop/royal-adire-kimono?ref=FUNMI10')
            ->assertOk()
            ->assertSessionHas('be_ref', 'funmi10')
            ->assertCookie('be_ref', 'funmi10');

        // Customer populates cart using CartManager
        session(['be_ref' => 'funmi10']);
        $cartManager = app(\App\Services\CartManager::class);
        $cartManager->add($product1->id, 'M', 2);
        $cartManager->add($product2->id, 'One Size', 1);

        Livewire::test(Checkout::class)
            ->set('email', 'buyer@example.com')
            ->set('name', 'Toyin Johnson')
            ->set('phone', '+2348099887766')
            ->set('address', '15 Victoria Island')
            ->set('city', 'Lagos')
            ->set('payment', 'card')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertSet('placed', true);

        // Verify Order is linked to executive
        $this->assertDatabaseHas('orders', [
            'email' => 'buyer@example.com',
            'business_executive_id' => $executive->id,
            'be_code' => 'funmi10',
        ]);

        // Verify commissions generated:
        // Product 1: 50,000 * 2 = 100,000 total sale. 10% commission = 10,000
        $this->assertDatabaseHas('be_commissions', [
            'business_executive_id' => $executive->id,
            'product_id' => $product1->id,
            'sale_amount' => 100000,
            'commission_amount' => 10000,
            'status' => 'pending',
        ]);

        // Product 2: 20,000 * 1 = 20,000 total sale. Fixed 3,000 * 1 = 3,000
        $this->assertDatabaseHas('be_commissions', [
            'business_executive_id' => $executive->id,
            'product_id' => $product2->id,
            'sale_amount' => 20000,
            'commission_amount' => 3000,
            'status' => 'pending',
        ]);

        // Executive balance
        $this->assertSame(13000, $executive->pendingCommission());
    }

    public function test_admin_can_mark_commissions_paid(): void
    {
        $admin = User::factory()->admin()->create();

        $executive = BusinessExecutive::factory()->create();

        $order = \App\Models\Order::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '+2348011223344',
            'address' => '12 Victoria Island',
            'city' => 'Lagos',
            'payment_method' => 'card',
            'subtotal' => 70000,
            'delivery' => 4000,
            'total' => 74000,
            'status' => 'confirmed',
            'business_executive_id' => $executive->id,
            'be_code' => $executive->code,
        ]);

        $comm1 = BeCommission::create([
            'business_executive_id' => $executive->id,
            'order_id' => $order->id,
            'product_id' => null,
            'product_name' => 'Linen Dress',
            'sale_amount' => 50000,
            'commission_amount' => 5000,
            'commission_rate' => '10%',
            'status' => 'pending',
        ]);

        $comm2 = BeCommission::create([
            'business_executive_id' => $executive->id,
            'order_id' => $order->id,
            'product_id' => null,
            'product_name' => 'Silk Turban',
            'sale_amount' => 20000,
            'commission_amount' => 2000,
            'commission_rate' => '10%',
            'status' => 'pending',
        ]);

        Livewire::actingAs($admin)
            ->test(Executives::class)
            ->call('viewExecutive', $executive->id)
            ->call('markAllPendingPaid', $executive->id)
            ->assertHasNoErrors();

        $this->assertSame('paid', $comm1->fresh()->status);
        $this->assertSame('paid', $comm2->fresh()->status);
        $this->assertNotNull($comm1->fresh()->paid_at);
        $this->assertSame(7000, $executive->paidCommission());
        $this->assertSame(0, $executive->pendingCommission());
    }

    public function test_admin_can_toggle_executive_status(): void
    {
        $admin = User::factory()->admin()->create();
        $executive = BusinessExecutive::factory()->create(['status' => 'active']);

        Livewire::actingAs($admin)
            ->test(Executives::class)
            ->call('toggleStatus', $executive->id);

        $this->assertSame('suspended', $executive->fresh()->status);

        Livewire::actingAs($admin)
            ->test(Executives::class)
            ->call('toggleStatus', $executive->id);

        $this->assertSame('active', $executive->fresh()->status);
    }
}
