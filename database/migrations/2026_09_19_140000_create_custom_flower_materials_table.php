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
        Schema::create('custom_flower_materials', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // 'flower', 'vase', 'filler', 'wrapping', 'accessory'
            $table->string('name'); // Örn: Kırmızı Ekvador Gülü, Şeffaf Silindir Vazo
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('daily_stock')->default(0); // Günlük taze mezat adedi
            $table->string('image_path')->nullable(); // Şeffaf PNG / WebP görseli
            $table->string('color_hex')->nullable(); // Renk kodu (#e11d48 vb.)
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->string('custom_title')->nullable()->after('product_id');
            $table->json('custom_design_data')->nullable()->after('custom_title');
            $table->string('custom_design_snapshot')->nullable()->after('custom_design_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['custom_title', 'custom_design_data', 'custom_design_snapshot']);
            $table->foreignId('product_id')->nullable(false)->change();
        });

        Schema::dropIfExists('custom_flower_materials');
    }
};
