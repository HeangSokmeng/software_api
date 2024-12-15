<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Specify the table name (optional if it matches the plural form of the model name)
    protected $table = 'categories';

    // Specify the primary key column
    protected $primaryKey = 'cate_id';

    // Indicate if the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the primary key type
    protected $keyType = 'int';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'cate_name',
        'cate_description',
    ];

    // Define columns to be treated as dates
    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:m:s',
        'updated_at' => 'datetime:d-M-Y',
        'date' => 'datetime:d-M-Y'
    ];
}
