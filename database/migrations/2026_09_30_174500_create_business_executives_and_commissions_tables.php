<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_executives', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('code')->unique();
            $table->string('invite_token')->unique();
            $table->string('status')->default('active'); // active, pending, suspended
            $table->unsignedInteger('default_commission_rate')->default(10);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('commission_type')->default('percent')->after('quantity'); // percent or fixed
            $table->unsignedInteger('commission_rate')->default(10)->after('commission_type');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('business_executive_id')->nullable()->after('user_id')->constrained('business_executives')->nullOnDelete();
            $table->string('be_code')->nullable()->after('business_executive_id');
        });

        Schema::create('be_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_executive_id')->constrained('business_executives')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('sale_amount');
            $table->unsignedInteger('commission_amount');
            $table->string('commission_rate');
            $table->string('status')->default('pending'); // pending, approved, paid, cancelled
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('be_commissions');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['business_executive_id']);
            $table->dropColumn(['business_executive_id', 'be_code']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_rate']);
        });

        Schema::dropIfExists('business_executives');
    }
};
