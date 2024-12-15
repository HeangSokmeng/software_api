<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReview extends Model
{
    use HasFactory;

    // Specify the table name (optional if it follows Laravel's conventions)
    protected $table = 'document_reviews';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'document_id',
        'docr_comment',
        'docr_rating',
    ];

    // Define relationships
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }
}
