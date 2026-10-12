<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_flower_materials', function (Blueprint $table) {
            $table->integer('stem_capacity')->default(25)->after('daily_stock');
        });
    }

    public function down(): void
    {
        Schema::table('custom_flower_materials', function (Blueprint $table) {
            $table->dropColumn('stem_capacity');
        });
    }
};
