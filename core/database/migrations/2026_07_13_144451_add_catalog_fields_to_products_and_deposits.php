<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'product_type')) {
                    $table->string('product_type', 40)->default('downloadable')->after('status');
                }

                if (!Schema::hasColumn('products', 'managed_by_admin')) {
                    $table->boolean('managed_by_admin')->default(0)->after('product_type');
                }

                if (!Schema::hasColumn('products', 'is_published')) {
                    $table->boolean('is_published')->default(0)->after('managed_by_admin');
                }

                if (!Schema::hasColumn('products', 'availability_status')) {
                    $table->string('availability_status', 40)->default('available')->after('is_published');
                }

                if (!Schema::hasColumn('products', 'base_price')) {
                    $table->decimal('base_price', 28, 8)->default(0)->after('availability_status');
                }
            });
        }

        if (Schema::hasTable('deposits') && !Schema::hasColumn('deposits', 'order_id')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->unsignedBigInteger('order_id')->nullable()->after('user_plan_id');
                $table->index('order_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                foreach (['product_type', 'managed_by_admin', 'is_published', 'availability_status', 'base_price'] as $column) {
                    if (Schema::hasColumn('products', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('deposits') && Schema::hasColumn('deposits', 'order_id')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->dropIndex(['order_id']);
                $table->dropColumn('order_id');
            });
        }
    }
};
