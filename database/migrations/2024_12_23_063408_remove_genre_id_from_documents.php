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
        Schema::table('documents', function (Blueprint $table) {
            $table->string('doc_file', 200)->nullable();
            $table->dropForeign(['genre_id']);
            $table->dropColumn('genre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('doc_file');
            $table->unsignedBigInteger('genre_id')->nullable();
            $table->foreign('genre_id')
                ->references('genr_id')
                ->on('genres')
                ->onDelete('set null');
        });
    }
};
