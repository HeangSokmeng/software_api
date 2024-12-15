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
        Schema::create('authors', function (Blueprint $table) {
            $table->id('auth_id');
            $table->string('auth_name', 100);
            $table->text('auth_bio')->nullable();
            $table->string('auth_email', 50)->nullable();
            $table->string('auth_gender', 15)->nullable();
            $table->text('auth_address')->nullable();
            $table->integer('auth_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
