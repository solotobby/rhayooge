<?php

namespace Tests\Feature;

use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(ProductSeeder::class);

        $this->get('/')->assertOk();
        $this->get('/shop')->assertOk();
        $this->get('/shop/ife-linen-dress')->assertOk()->assertSee('Ifẹ́ Linen Dress');
        $this->get('/about')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/account')->assertOk()->assertSee('Welcome back.');
    }
}
