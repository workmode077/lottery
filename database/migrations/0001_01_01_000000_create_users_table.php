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

        // Limit
        $table->bigInteger('daily_credit_limit')->default(5000)->comment('daily credit limit');
        $table->bigInteger('weekly_credit_limit')->default(35000)->comment('weekly credit limit');
        $table->bigInteger('monthly_credit_limit')->default(150000)->comment('monthly credit limit');
        $table->bigInteger('yearly_credit_limit')->default(1800000)->comment('yearly credit limit');

        //RATE
       // Super Agent Rate & Commission
        $table->bigInteger('super_rate')->default(8)->comment('Super agent rate');
        $table->bigInteger('super_commission_rate')->default(2)->comment('Super agent commission rate');

        // Agent Rate & Commission
        $table->bigInteger('a_rate')->default(8)->comment('Agent rate');
        $table->bigInteger('a_commission_rate')->default(2)->comment('Agent commission rate');

        // B Agent Rate & Commission
        $table->bigInteger('b_rate')->default(8)->comment('B agent rate');
        $table->bigInteger('b_commission_rate')->default(2)->comment('B agent commission rate');

        // C Agent Rate & Commission
        $table->bigInteger('c_rate')->default(8)->comment('C agent rate');
        $table->bigInteger('c_commission_rate')->default(2)->comment('C agent commission rate');

        // AB Rate & Commission
        $table->bigInteger('ab_rate')->default(8)->comment('AB rate');
        $table->bigInteger('ab_commission_rate')->default(2)->comment('AB commission rate');

        // BC Rate & Commission
        $table->bigInteger('bc_rate')->default(8)->comment('BC rate');
        $table->bigInteger('bc_commission_rate')->default(2)->comment('BC commission rate');

        // AC Rate & Commission
        $table->bigInteger('ac_rate')->default(8)->comment('AC rate');
        $table->bigInteger('ac_commission_rate')->default(2)->comment('AC commission rate');

        // Box Rate & Commission
        $table->bigInteger('box_rate')->default(8)->comment('Box rate');
        $table->bigInteger('box_commission_rate')->default(2)->comment('Box commission rate');

 

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
