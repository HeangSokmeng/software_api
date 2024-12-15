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
        Schema::create('users', function (Blueprint $table)
        {
            $table->id();
            $table->string('code',35)->nullable();
            $table->string('first_name',50)->nullable();
            $table->string('last_name',50)->nullable();
            $table->string('username',100)->nullable();
            $table->string('phone',25);
            $table->enum('account_type', ['superadmin', 'admin', 'teacher', 'student', 'other']);
            $table->string('gender',7)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',300)->nullable();
            $table->boolean('system_admin')->default(false);
            $table->unsignedBigInteger('create_uid');
            $table->unsignedBigInteger('update_uid');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('company_id');
            $table->timestampTz("created_at")->useCurrent();

            $table->timestampTz("updated_at")->useCurrent()->useCurrentOnUpdate();
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
