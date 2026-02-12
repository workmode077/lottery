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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->comment('User ID who owns the bill');
            $table->unsignedBigInteger('game_id')->comment('Game ID associated with the bill');

            $table->string('customer_name')->nullable();
            $table->integer('total_count')->nullable();
            $table->integer('total_rate')->default(0);
            $table->integer('total_commission')->default(0);
            $table->integer('reduced_commission')->nullable();
            $table->integer('total_amount')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // ✅ Foreign keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('game_id')
                  ->references('id')
                  ->on('games')
                  ->onDelete('cascade');

            // ✅ index syntax
            $table->index(['user_id', 'game_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
