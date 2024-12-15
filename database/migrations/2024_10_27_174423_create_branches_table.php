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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->nullable();
            $table->string('name_kh',150)->nullable();
            $table->string('address',150)->nullable();
            $table->string('address_kh',300)->nullable();
            $table->string('description')->nullable();
            $table->string('phone',25)->nullable();
            $table->timestampTz("created_at")->useCurrent();
            $table->timestampTz("updated_at")->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('create_uid')->nullable();
            $table->unsignedBigInteger('update_uid')->nullable();
            $table->unsignedBigInteger('company_id');
        /**
         * relationship
        */
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->boolean('is_deleted')->default(0);
            $table->unsignedBigInteger('deleted_uid')->nullable();
            $table->dateTime('deleted_datetime')->nullable();
            $table->foreign('deleted_uid')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
