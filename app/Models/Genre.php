<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    // Specify the table name (optional if it follows Laravel's conventions)
    protected $table = 'genres';

    // Specify the primary key column
    protected $primaryKey = 'genr_id';

    // Indicate if the primary key is auto-incrementing
    public $incrementing = true;

    // Define the primary key type
    protected $keyType = 'int';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'genr_name',
        'genr_description',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:m:s',
        'updated_at' => 'datetime:d-M-Y',
        'date' => 'datetime:d-M-Y'
    ];
}
