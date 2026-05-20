<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected$connection = 'wordpress';
    protected $table = 'ism13qf_users'; 
    protected $primaryKey = 'ID'; 
    public $timestamps = false; 

    protected $fillable = [
        'user_login',
        'user_nicename',
        'user_email',
        'user_pass',
        'display_name',
    ];

    protected $hidden = [
        'user_pass',
        'remember_token',
    ];

    public function getAuthIdentifierName()
    {
        return 'ID';
    }

    public function getAuthPassword()
    {
        return $this->user_pass;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
}