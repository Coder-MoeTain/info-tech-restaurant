<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->decimal('tip_amount', 12, 2)->default(0);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('void_reason')->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('station')->nullable();
            $table->foreignId('printer_id')->nullable()->constrained('printers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'void_reason']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_phone', 'customer_address', 'tip_amount']);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['station', 'printer_id']);
        });
    }
};
