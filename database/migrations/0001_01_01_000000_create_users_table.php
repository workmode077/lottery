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
       Schema::create('users', function (Blueprint $table) {
        $table->id(); 
        $table->string('username');
        $table->enum('user_type', ['super_agent', 'agent', 'sub_agent']);
        $table->unsignedBigInteger('parent_id')->nullable()->comment('Parent user ID for hierarchy');
        $table->string('password');
        $table->string('plain_password');

        $table->bigInteger('daily_credit_limit')->default(10000)->nullable();
        $table->bigInteger('weekly_credit_limit')->default(50000)->nullable();
        $table->bigInteger('monthly_credit_limit')->default(100000)->nullable();
        $table->bigInteger('yearly_credit_limit')->default(1000000)->nullable();

        $table->bigInteger('super_rate')->default(8)->nullable();
        $table->bigInteger('super_commission_rate')->default(2)->nullable();

        $table->bigInteger('a_rate')->default(8)->nullable();
        $table->bigInteger('a_commission_rate')->default(2)->nullable();

        $table->bigInteger('b_rate')->default(8)->nullable();
        $table->bigInteger('b_commission_rate')->default(2)->nullable();

        $table->bigInteger('c_rate')->default(8)->nullable();
        $table->bigInteger('c_commission_rate')->default(2)->nullable();

        $table->bigInteger('ab_rate')->default(8)->nullable();
        $table->bigInteger('ab_commission_rate')->default(2)->nullable();

        $table->bigInteger('bc_rate')->default(8)->nullable();
        $table->bigInteger('bc_commission_rate')->default(2)->nullable();

        $table->bigInteger('ac_rate')->default(8)->nullable();
        $table->bigInteger('ac_commission_rate')->default(2)->nullable();

        $table->bigInteger('box_rate')->default(8)->nullable();
        $table->bigInteger('box_commission_rate')->default(2)->nullable();


        // ===== LSK SUPER COMMISSION =====

        $table->string('lsk_super_first_price_commission')->default("2");

        $table->string('lsk_super_second_price_commission')->default("2");

        $table->string('lsk_super_third_price_commission')->default("2");

        $table->string('lsk_super_fourth_price_commission')->default("2");

        $table->string('lsk_super_fifth_price_commission')->default("2");

        $table->string('lsk_super_sixth_price_commission')->default("2");

        $table->boolean('lsk_super_lsk_30')->default(true);

        $table->string('lsk_super_seventh_price_commission')->default("2");

        $table->boolean('lsk_super_lsk_50')->default(true);


        // ===== BOX: THREE DIFFERENT NUMBER =====

        $table->string('box_three_diff_first_price_commission')->default("2");

        $table->string('box_three_diff_second_price_commission')->default("2");


        // ===== BOX: TWO SAME NUMBER =====

        $table->string('box_two_same_first_price_commission')->default("2");

        $table->string('box_two_same_second_price_commission')->default("2");


        // ===== BOX: THREE SAME NUMBER =====

        $table->string('box_three_same_first_price_commission')->default("2");


        // ===== ABC COMMISSION =====

        $table->string('abc_first_price_commission')->default("2");

        $table->string('abc_second_price_commission')->default("2");


        // ===== AB_AC_BC COMMISSION =====

        $table->string('ab_ac_bc_first_price_commission')->default("2");

        $table->string('ab_ac_bc_second_price_commission')->default("2");


        // ===== GAME COUNT LIMIT SETTINGS =====

        $table->boolean('game_count_editable')->default(true);

        $table->boolean('game_count_all_dear')->default(true);
        $table->boolean('game_count_all_game')->default(false);

        $table->string('game_count_super')->default("200");
        $table->string('game_count_box')->default("300");

        $table->string('game_count_a')->default("500");
        $table->string('game_count_b')->default("500");
        $table->string('game_count_c')->default("500");

        $table->string('game_count_ab')->default("500");
        $table->string('game_count_ac')->default("500");
        $table->string('game_count_bc')->default("500");




        $table->boolean('status')->default(true)->comment('active status');
        $table->rememberToken();
        $table->softDeletes();
        $table->timestamps();

        // Constraints
        $table->unique(['username', 'user_type']); 
        $table->index(['username', 'user_type', 'status']);

        // Optional: foreign key for parent relationship
        $table->foreign('parent_id')->references('id')->on('users')->onDelete('set null');
    });




        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
