<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table ='roles';
    protected $fillable = [
        'name',
        'description',
        'create_uid',
        'update_uid',
        'company_id',
        'branch_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime:d-M-Y H:m:s',
            'password' => 'hashed',
            'created_at' => 'datetime:d-M-Y H:m:s',
            'updated_at' => 'datetime:d-M-Y H:m:s'
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'name' => $this->username
        ];
    }

    public function creator(){
        return $this->belongsTo(User::class, 'create_uid', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

}
