<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_location_id')->nullable()->constrained('shipping_locations')->nullOnDelete()->after('shipping_address');
            $table->string('shipping_location_name')->nullable()->after('shipping_location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_location_id']);
            $table->dropColumn(['shipping_location_id', 'shipping_location_name']);
        });
    }
};
