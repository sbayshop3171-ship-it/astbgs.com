<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'wallet_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('wallet_balance', 28, 8)->default(0)->after('balance');
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'balance_type')) {
                    $table->string('balance_type', 40)->nullable()->after('charge');
                    $table->index('balance_type');
                }

                if (!Schema::hasColumn('transactions', 'reference_type')) {
                    $table->string('reference_type', 40)->nullable()->after('balance_type');
                }

                if (!Schema::hasColumn('transactions', 'reference_id')) {
                    $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
                    $table->index(['reference_type', 'reference_id']);
                }
            });
        }

        if (Schema::hasTable('deposits') && !Schema::hasColumn('deposits', 'purpose')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->string('purpose', 40)->nullable()->after('order_id');
                $table->index('purpose');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'wallet_amount')) {
                    $table->decimal('wallet_amount', 28, 8)->default(0)->after('total');
                }

                if (!Schema::hasColumn('orders', 'payment_source')) {
                    $table->string('payment_source', 40)->nullable()->after('gateway_name');
                    $table->index('payment_source');
                }
            });
        }

        if (Schema::hasTable('user_plans')) {
            Schema::table('user_plans', function (Blueprint $table) {
                if (!Schema::hasColumn('user_plans', 'payment_source')) {
                    $table->string('payment_source', 40)->nullable()->after('price');
                    $table->index('payment_source');
                }

                if (!Schema::hasColumn('user_plans', 'payment_trx')) {
                    $table->string('payment_trx')->nullable()->after('payment_source');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_plans')) {
            Schema::table('user_plans', function (Blueprint $table) {
                if (Schema::hasColumn('user_plans', 'payment_trx')) {
                    $table->dropColumn('payment_trx');
                }

                if (Schema::hasColumn('user_plans', 'payment_source')) {
                    $table->dropIndex(['payment_source']);
                    $table->dropColumn('payment_source');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'payment_source')) {
                    $table->dropIndex(['payment_source']);
                    $table->dropColumn('payment_source');
                }

                if (Schema::hasColumn('orders', 'wallet_amount')) {
                    $table->dropColumn('wallet_amount');
                }
            });
        }

        if (Schema::hasTable('deposits') && Schema::hasColumn('deposits', 'purpose')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->dropIndex(['purpose']);
                $table->dropColumn('purpose');
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'reference_id')) {
                    $table->dropIndex(['reference_type', 'reference_id']);
                    $table->dropColumn('reference_id');
                }

                if (Schema::hasColumn('transactions', 'reference_type')) {
                    $table->dropColumn('reference_type');
                }

                if (Schema::hasColumn('transactions', 'balance_type')) {
                    $table->dropIndex(['balance_type']);
                    $table->dropColumn('balance_type');
                }
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'wallet_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('wallet_balance');
            });
        }
    }
};
