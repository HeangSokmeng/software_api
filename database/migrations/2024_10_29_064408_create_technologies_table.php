<?php

use App\Traits\BaseMigrationField;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use BaseMigrationField;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('technologies', function (Blueprint $table) {
            $this->AddBaseFields($table);
            $table->string('name')->nullable()->after('id');
            $table->string('version')->nullable()->after('name');
            $table->boolean('is_active')->nullable()->after('version');
            $table->string('description')->nullable();
            $table->string('photo_file_name',200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technologies');
    }
};
