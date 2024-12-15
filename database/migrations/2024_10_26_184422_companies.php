<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
            Schema::create('companies', function (Blueprint $table)
            {
                $table->id();
                $table->string('photo_file_name', 200)->nullable();
                $table->string('name_kh')->nullable();
                $table->string('name');
                $table->string('address_kh')->nullable();
                $table->string('address')->nullable();
                $table->string('email')->nullable();
                $table->string('website')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('cp_name')->nullable();
                $table->text('description')->nullable();
                $table->string('company_type', 100)->nullable();

                $table->unsignedBigInteger('create_uid')->nullable();
                $table->unsignedBigInteger('update_uid')->nullable();
                $table->timestampsTz();
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
