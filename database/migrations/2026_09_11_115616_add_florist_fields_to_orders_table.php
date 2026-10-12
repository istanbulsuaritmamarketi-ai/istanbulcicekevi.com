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
            $table->string('recipient_name')->nullable()->after('customer_name');
            $table->string('recipient_phone')->nullable()->after('recipient_name');
            $table->string('sender_name')->nullable()->after('recipient_phone');
            $table->string('sender_phone')->nullable()->after('sender_name');
            $table->boolean('is_anonymous')->default(false)->after('sender_phone');
            $table->string('delivery_date')->nullable()->after('is_anonymous');
            $table->string('delivery_time_slot')->nullable()->after('delivery_date');
            $table->text('card_note')->nullable()->after('delivery_time_slot');
            $table->string('card_sender_title')->nullable()->after('card_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'recipient_phone',
                'sender_name',
                'sender_phone',
                'is_anonymous',
                'delivery_date',
                'delivery_time_slot',
                'card_note',
                'card_sender_title',
            ]);
        });
    }
};
