<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WordpressUser extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'ism13qf_users'; 
    protected $primaryKey = 'ID';
    public $timestamps = false;
}