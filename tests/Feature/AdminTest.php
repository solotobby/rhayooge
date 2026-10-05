<?php

namespace Tests\Feature;

use App\Livewire\Admin\Login;
use App\Livewire\Admin\ProductForm;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_dharmie_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_customer_cannot_open_the_desk(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_view_the_desk(): void
    {
        $admin = User::factory()->admin()->create();
        $this->seed(ProductSeeder::class);

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('The desk.');
        $this->actingAs($admin)->get('/admin/products')->assertOk();
        $this->actingAs($admin)->get('/admin/products/new')->assertOk()->assertSee('Collection Studio');
        $this->actingAs($admin)->get('/admin/executives')->assertOk()->assertSee('Business Executives');
        $this->actingAs($admin)->get('/admin/orders')->assertOk();
        $this->actingAs($admin)->get('/admin/messages')->assertOk();
        $this->actingAs($admin)->get('/admin/clients')->assertOk();
    }

    public function test_admin_can_sign_in_at_the_desk(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'dharmie@rhayooge.com',
        ]);

        Livewire::test(Login::class)
            ->set('email', 'dharmie@rhayooge.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_customer_credentials_are_rejected_at_the_desk(): void
    {
        User::factory()->create(['email' => 'client@example.com']);

        Livewire::test(Login::class)
            ->set('email', 'client@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_add_a_piece(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProductForm::class)
            ->set('name', 'Studio Slip Dress')
            ->set('slug', 'studio-slip-dress')
            ->set('category', 'Dresses')
            ->set('price', '54000')
            ->set('description', 'A considered column in warm ivory, cut to move with the body.')
            ->set('image', 'https://images.unsplash.com/photo-1496747611176-843222e1e57c')
            ->set('selectedSizes', ['S', 'M', 'L'])
            ->call('save')
            ->assertRedirect(route('admin.products'));

        $this->assertDatabaseHas('products', [
            'slug' => 'studio-slip-dress',
            'name' => 'Studio Slip Dress',
            'price' => 54000,
        ]);
    }

    public function test_admin_can_edit_a_piece(): void
    {
        $admin = User::factory()->admin()->create();
        $this->seed(ProductSeeder::class);
        $product = Product::query()->where('slug', 'ife-linen-dress')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/products/ife-linen-dress/edit')
            ->assertOk()
            ->assertSee('Edit piece')
            ->assertSee('ife-linen-dress');

        Livewire::actingAs($admin)
            ->test(ProductForm::class, ['product' => $product])
            ->set('price', '47000')
            ->call('save')
            ->assertRedirect(route('admin.products'));

        $this->assertSame(47000, $product->fresh()->price);
    }

    public function test_admin_can_add_piece_with_discount_quantity_and_numeric_sizes(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProductForm::class)
            ->set('name', 'Adire Linen Caftan')
            ->assertSet('slug', 'adire-linen-caftan')
            ->set('slug', 'custom-adire-caftan')
            ->set('category', 'Dresses')
            ->set('price', '40000')
            ->set('original_price', '50000')
            ->set('quantity', 15)
            ->call('setSizeMode', 'number')
            ->set('selectedSizes', ['8', '10', '12', '14'])
            ->set('description', 'A considered caftan in hand-dyed indigo linen with relaxed drape.')
            ->set('image', 'https://images.unsplash.com/photo-1496747611176-843222e1e57c')
            ->call('save')
            ->assertRedirect(route('admin.products'));

        $this->assertDatabaseHas('products', [
            'slug' => 'custom-adire-caftan',
            'name' => 'Adire Linen Caftan',
            'price' => 40000,
            'original_price' => 50000,
            'quantity' => 15,
            'size_mode' => 'number',
        ]);

        $created = Product::query()->where('slug', 'custom-adire-caftan')->firstOrFail();
        $this->assertTrue($created->hasDiscount());
        $this->assertSame(20, $created->discountPercent());
        $this->assertSame('₦50,000', $created->formattedOriginalPrice());
        $this->assertTrue($created->inStock());
        $this->assertSame(['8', '10', '12', '14'], $created->sizes);
    }

    public function test_admin_can_auto_generate_and_update_slug(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)
            ->test(ProductForm::class)
            ->set('name', 'Sculpted Gold Cuff')
            ->assertSet('slug', 'sculpted-gold-cuff');

        // Now manually update it
        $component->set('slug', 'limited-edition-gold-cuff')
            ->assertSet('slug', 'limited-edition-gold-cuff');

        // Clicking generate re-syncs from name
        $component->call('generateSlug')
            ->assertSet('slug', 'sculpted-gold-cuff');
    }

    public function test_admin_can_manage_multiple_images_for_a_product(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProductForm::class)
            ->set('name', 'Terracotta Silk Kimono')
            ->set('slug', 'terracotta-silk-kimono')
            ->set('category', 'Outerwear')
            ->set('price', '65000')
            ->set('description', 'A sweeping kimono cut from fluid hammered silk with wide batwing sleeves.')
            ->set('image', 'https://images.unsplash.com/photo-1595777457583-95e059d581b8')
            ->set('newImageUrl', 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446')
            ->call('addAdditionalImage')
            ->set('newImageUrl', 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1')
            ->call('addAdditionalImage')
            ->assertCount('additionalImages', 2)
            ->call('save')
            ->assertRedirect(route('admin.products'));

        $product = Product::where('slug', 'terracotta-silk-kimono')->firstOrFail();
        $this->assertCount(3, $product->galleryImages());
        $this->assertSame('https://images.unsplash.com/photo-1595777457583-95e059d581b8', $product->primaryImage());
    }

    public function test_admin_can_use_multi_step_wizard_with_discount_percentage_and_size_stock_inventory(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)
            ->test(ProductForm::class)
            ->assertSet('currentStep', 1)
            // Step 1: Basics
            ->set('name', 'Monochrome Pleated Tunic')
            ->set('slug', 'monochrome-pleated-tunic')
            ->set('category', 'Tops')
            ->set('description', 'An asymmetric pleated tunic cut from crisp hand-woven cotton.')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            // Step 2: Pricing & Commission (Original price + discount percentage)
            ->set('original_price', '80000')
            ->set('discount_percent', 25)
            ->assertSet('price', '60000') // 80,000 * (1 - 0.25) = 60,000
            ->set('commission_type', 'percent')
            ->set('commission_rate', 15)
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            // Step 3: Sizes & Stock Inventory per Size
            ->call('setSizeMode', 'letter')
            ->set('selectedSizes', ['S', 'M', 'L'])
            ->set('sizeStocks', ['S' => 8, 'M' => 12, 'L' => 6])
            ->call('syncTotalQuantity')
            ->assertSet('quantity', 26) // 8 + 12 + 6 = 26
            ->call('nextStep')
            ->assertSet('currentStep', 4)
            // Step 4: Primary Cover Photo
            ->set('image', 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b')
            ->call('nextStep')
            ->assertSet('currentStep', 5)
            // Step 5: Save & Publish
            ->call('save', 'published')
            ->assertRedirect(route('admin.products'));

        $product = Product::query()->where('slug', 'monochrome-pleated-tunic')->firstOrFail();
        $this->assertSame('published', $product->status);
        $this->assertTrue($product->isPublished());
        $this->assertSame(60000, $product->price);
        $this->assertSame(80000, $product->original_price);
        $this->assertTrue($product->hasDiscount());
        $this->assertSame(25, $product->discountPercent());
        $this->assertSame(26, $product->quantity);
        $this->assertSame(['S' => 8, 'M' => 12, 'L' => 6], $product->size_inventory);
        $this->assertSame(8, $product->stockForSize('S'));
        $this->assertSame(12, $product->stockForSize('M'));
        $this->assertSame(6, $product->stockForSize('L'));
        $this->assertSame(0, $product->stockForSize('XL'));
        $this->assertTrue($product->isSizeInStock('S'));
        $this->assertFalse($product->isSizeInStock('XL'));
    }

    public function test_admin_can_create_draft_product_which_is_hidden_from_public_and_executives(): void
    {
        $admin = User::factory()->admin()->create();

        // Create product as draft
        Livewire::actingAs($admin)
            ->test(ProductForm::class)
            ->set('name', 'Secret Atelier Prototype')
            ->set('slug', 'secret-atelier-prototype')
            ->set('category', 'Outerwear')
            ->set('description', 'An experimental coat in brushed wool, strictly internal testing.')
            ->set('original_price', '75000')
            ->set('discount_percent', 0)
            ->set('selectedSizes', ['One size'])
            ->set('sizeStocks', ['One size' => 2])
            ->set('image', 'https://images.unsplash.com/photo-1544441893-675973e31985')
            ->call('save', 'draft')
            ->assertRedirect(route('admin.products'));

        $draft = Product::query()->where('slug', 'secret-atelier-prototype')->firstOrFail();
        $this->assertSame('draft', $draft->status);
        $this->assertTrue($draft->isDraft());
        $this->assertFalse($draft->isPublished());

        // Unauthenticated visitor
        auth()->logout();

        // Hidden from public shop
        $this->get('/shop')
            ->assertOk()
            ->assertDontSee('Secret Atelier Prototype');

        // Hidden from direct public product page
        $this->get('/shop/secret-atelier-prototype')
            ->assertNotFound();

        // Accessible to authenticated admin
        $this->actingAs($admin)
            ->get('/shop/secret-atelier-prototype')
            ->assertOk()
            ->assertSee('Secret Atelier Prototype');

        // Admin products table displays draft and allows toggling to published
        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Products::class)
            ->assertSee('Secret Atelier Prototype')
            ->call('toggleStatus', $draft->id);

        $this->assertSame('published', $draft->fresh()->status);
        $this->assertTrue($draft->fresh()->isPublished());

        // Now visible in public shop
        $this->get('/shop')
            ->assertOk()
            ->assertSee('Secret Atelier Prototype');
    }

    public function test_admin_can_inspect_piece_in_admin_modal(): void
    {
        $admin = User::factory()->admin()->create();
        $this->seed(ProductSeeder::class);
        $product = Product::query()->where('slug', 'ife-linen-dress')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Products::class)
            ->assertSet('showInspectModal', false)
            ->call('inspectProduct', $product->id)
            ->assertSet('showInspectModal', true)
            ->assertSet('inspectingProductId', $product->id)
            ->assertSee('Piece Inspection')
            ->assertSee($product->name)
            ->assertSee($product->formattedPrice())
            ->call('closeInspectModal')
            ->assertSet('showInspectModal', false);
    }
}

