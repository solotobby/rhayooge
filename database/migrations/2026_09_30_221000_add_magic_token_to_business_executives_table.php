<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_executives', function (Blueprint $table) {
            $table->string('magic_token', 64)->nullable()->after('invite_token')->index();
            $table->timestamp('magic_token_expires_at')->nullable()->after('magic_token');
        });
    }

    public function down(): void
    {
        Schema::table('business_executives', function (Blueprint $table) {
            $table->dropIndex(['magic_token']);
            $table->dropColumn(['magic_token', 'magic_token_expires_at']);
        });
    }
};
