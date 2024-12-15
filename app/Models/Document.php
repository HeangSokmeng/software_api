<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // Specify the table name (optional if it follows Laravel's conventions)
    protected $table = 'documents';

    // Specify the primary key column
    protected $primaryKey = 'id'; // Default 'id', update if different.

    // Indicate if the primary key is auto-incrementing
    public $incrementing = true;

    // Define the primary key type
    protected $keyType = 'int';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'doc_name',
        'doc_title',
        'doc_color',
        'doc_size',
        'doc_page',
        'doc_created_date',
        'doc_published_date',
        'doc_publication_year',
        'doc_keywords',
        'doc_photo',
        'author_id',
        'category_id',
        'genre_id',
        'doc_type',
        'create_uid',
        'update_uid',
        'company_id',
        'branch_id'
    ];

    // Specify columns to be treated as dates
    protected $dates = [
        'doc_created_date',
        'doc_published_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:m:s',
        'updated_at' => 'datetime:d-M-Y',
        'date' => 'datetime:d-M-Y',
        'doc_created_date' => 'datetime:d-M-Y',
        'doc_published_date' => 'datetime:d-M-Y',
        'doc_publication_year' => 'datetime:d-M-Y',
    ];

    // Define relationships
    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id', 'auth_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'cate_id');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id', 'genr_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'create_uid', 'id');
    }
}
