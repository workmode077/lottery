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
        Schema::create('result_entries', function (Blueprint $table) {
        $table->id();

        // Reference to games table
        $table->unsignedBigInteger('game_id')->comment('Reference to games table');
        $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

        // Date and time of the result
        $table->dateTime('date');

        // Prize columns (3-digit numbers)
        $table->unsignedSmallInteger('prize_one')->comment('3-digit number');
        $table->unsignedSmallInteger('prize_two')->comment('3-digit number');
        $table->unsignedSmallInteger('prize_three')->comment('3-digit number');
        $table->unsignedSmallInteger('prize_four')->comment('3-digit number');
        $table->unsignedSmallInteger('prize_five')->comment('3-digit number');

        // Number columns (1 to 30) all 3-digit numbers
        $columns = [
            'one','two','three','four','five','six','seven','eight','nine','ten',
            'eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen','twenty',
            'twenty_one','twenty_two','twenty_three','twenty_four','twenty_five','twenty_six','twenty_seven','twenty_eight','twenty_nine','thirty'
        ];

        foreach ($columns as $col) {
            $table->unsignedSmallInteger($col)
                ->comment('3-digit number');
        }
         $table->text('link')->comment('link');
        // Status enum
        $table->boolean('status')->default(true)->comment('active status');

        // Timestamps and soft deletes
        $table->timestamps();
        $table->softDeletes();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_entries');
    }
};
