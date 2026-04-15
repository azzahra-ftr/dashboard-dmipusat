<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'ism13qf_tec_events';
    protected $primaryKey = 'event_id';
    public $timestamps = false;

    protected $casts = [
        'start_date' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(WordpressPost::class, 'post_id', 'ID');
    }
}