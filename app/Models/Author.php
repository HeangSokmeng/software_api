<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    // Specify the table name (optional if it matches the plural form of the model name)
    protected $table = 'authors';

    // Primary key column
    protected $primaryKey = 'auth_id';

    // Specify if the primary key is not auto-incrementing
    public $incrementing = true;

    // Specify the primary key type if it's not an integer
    protected $keyType = 'int';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'auth_name',
        'auth_bio',
        'auth_email',
        'auth_gender',
        'auth_address',
        'auth_phone',
    ];

    // Define columns to be treated as dates
    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:m:s',
        'updated_at' => 'datetime:d-M-Y',
        'date' => 'datetime:d-M-Y'
    ];
}
