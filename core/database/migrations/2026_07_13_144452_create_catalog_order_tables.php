<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_options')) {
            Schema::create('product_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('name');
                $table->decimal('price', 28, 8)->default(0);
                $table->decimal('min_amount', 28, 8)->nullable();
                $table->decimal('max_amount', 28, 8)->nullable();
                $table->text('availability_note')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(1);
                $table->timestamps();

                $table->index('product_id');
            });
        }

        if (!Schema::hasTable('product_files')) {
            Schema::create('product_files', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('product_option_id')->nullable();
                $table->string('display_name');
                $table->string('stored_name');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(1);
                $table->timestamps();

                $table->index('product_id');
                $table->index('product_option_id');
            });
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('order_number')->unique();
                $table->string('status', 40)->default('pending_payment');
                $table->string('currency', 40)->default('USD');
                $table->decimal('subtotal', 28, 8)->default(0);
                $table->decimal('total', 28, 8)->default(0);
                $table->text('internal_note')->nullable();
                $table->text('customer_note')->nullable();
                $table->string('payment_trx')->nullable();
                $table->string('gateway_name')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('status');
            });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('product_option_id')->nullable();
                $table->string('title');
                $table->string('delivery_type', 40);
                $table->string('option_name')->nullable();
                $table->decimal('unit_price', 28, 8)->default(0);
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('line_total', 28, 8)->default(0);
                $table->json('detail')->nullable();
                $table->unsignedInteger('download_count')->default(0);
                $table->timestamp('last_downloaded_at')->nullable();
                $table->timestamps();

                $table->index('order_id');
                $table->index('product_id');
                $table->index('product_option_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_files');
        Schema::dropIfExists('product_options');
    }
};
