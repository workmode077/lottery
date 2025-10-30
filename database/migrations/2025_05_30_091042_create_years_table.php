<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('years', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('year')->comment('4-digit year');
            $table->string('slug', 50);
            $table->boolean('status')->default(true)->comment('Year active status')->index();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('slug');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('years');
    }
};
