<?php

use App\Traits\BaseMigrationField;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use BaseMigrationField;

    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $this->AddBaseFields($table);
            $table->string('doc_name', 50);
            $table->text('doc_title')->nullable();
            $table->string('doc_color', 15)->nullable();
            $table->string('doc_size', 10)->nullable();
            $table->integer('doc_page')->nullable();
            $table->date('doc_created_date')->nullable();
            $table->date('doc_published_date')->nullable();
            $table->integer('doc_publication_year');
            $table->text('doc_keywords')->nullable();
            $table->string('doc_photo', 200)->nullable();

            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('genre_id')->nullable();

            $table->enum('doc_type', ['book', 'newspaper', 'project', 'other'])->default('other');

            $table->foreign('author_id')->references('auth_id')->on('authors')->onDelete('set null');
            $table->foreign('category_id')->references('cate_id')->on('categories')->onDelete('set null');
            $table->foreign('genre_id')->references('genr_id')->on('genres')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
