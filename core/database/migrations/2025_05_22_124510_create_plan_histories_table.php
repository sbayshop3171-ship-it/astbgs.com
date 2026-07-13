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
        if (Schema::hasTable('plan_histories')) {
            return;
        }

        Schema::create('plan_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id')->default(0);
            $table->decimal('amount', 28, 8)->default(0);
            $table->string('history_type', 40)->default('0');
            $table->string('remark', 40)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_histories');
    }
};
