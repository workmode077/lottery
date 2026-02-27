<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            

            // ===== LSK SUPER COMMISSION =====
            $table->unsignedBigInteger('lsk_super_first_price')->default(5000);
            $table->unsignedBigInteger('lsk_super_second_price')->default(3000);
            $table->unsignedBigInteger('lsk_super_third_price')->default(2000);
            $table->unsignedBigInteger('lsk_super_fourth_price')->default(1000);
            $table->unsignedBigInteger('lsk_super_fifth_price')->default(500);
            $table->unsignedBigInteger('lsk_super_sixth_price')->default(200);
            $table->boolean('lsk_super_30_enabled')->default(true);
            $table->unsignedBigInteger('lsk_super_seventh_price')->default(100);
            $table->boolean('lsk_super_50_enabled')->default(true);

            // ===== BOX: THREE DIFFERENT NUMBER =====
            $table->unsignedBigInteger('box_three_diff_first_price')->default(5000);
            $table->unsignedBigInteger('box_three_diff_second_price')->default(3000);

            // ===== BOX: TWO SAME NUMBER =====
            $table->unsignedBigInteger('box_two_same_first_price')->default(5000);
            $table->unsignedBigInteger('box_two_same_second_price')->default(3000);

            // ===== BOX: THREE SAME NUMBER =====
            $table->unsignedBigInteger('box_three_same_first_price')->default(5000);

            // ===== ABC COMMISSION =====
            $table->unsignedBigInteger('abc_first_price')->default(5000);
            $table->unsignedBigInteger('abc_second_price')->default(3000);

            // ===== AB_AC_BC COMMISSION =====
            $table->unsignedBigInteger('ab_ac_bc_first_price')->default(5000);
            $table->unsignedBigInteger('ab_ac_bc_second_price')->default(3000);
            $table->softDeletes();
            $table->timestamps();
        });
            // ✅ Insert default row
            DB::table('prices')->insert([
                'created_at' => now(),
                'updated_at' => now(),
            ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
