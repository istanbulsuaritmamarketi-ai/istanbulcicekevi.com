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
        Schema::dropIfExists('gift_product');
        Schema::dropIfExists('gifts');

        if (Schema::hasColumn('products', 'has_gift')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('has_gift');
            });
        }

        if (Schema::hasColumn('products', 'gift_note')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('gift_note');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed as gift system is completely decommissioned
    }
};
