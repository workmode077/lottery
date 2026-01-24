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
        Schema::create('bill_items', function (Blueprint $table) {
             $table->id();

            $table->unsignedBigInteger('bill_id')->comment('Associated Bill ID');

            $table->enum('type', [
                'SUPER',
                'BOX',
                'A',
                'B',
                'C',
                'AB',
                'AC',
                'BC'
            ])->comment('Item type');

            $table->unsignedInteger('number');
            $table->unsignedInteger('count')->default(1);
            $table->unsignedInteger('price');

            $table->softDeletes(); 
            $table->timestamps();

            // ✅ Foreign key
            $table->foreign('bill_id')
                  ->references('id')
                  ->on('bills')
                  ->onDelete('cascade');

            // ✅ Index
            $table->index(['bill_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
