<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table ='services';
    protected $fillable = [
        'name',
        'description',
        'date',
        'photo_file_name',
        'create_uid',
        'update_uid',
        'company_id'
    ];

    protected $hidden = [
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:m:s',
        'updated_at' => 'datetime:d-M-Y',
        'date' => 'datetime:d-M-Y'
    ];

    public function creator() {
        return $this->belongsTo(User::class, 'create_uid');
    }


}
