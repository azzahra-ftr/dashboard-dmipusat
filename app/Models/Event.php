<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'ism13qf_tec_events';
    protected $primaryKey = 'event_id';
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'start_date',
        'end_date',
        'timezone',
        'start_date_utc',
        'end_date_utc',
        'duration'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(WordpressPost::class, 'post_id', 'ID');
    }

    public function getMetaValue($key, $default = null)
    {
        if (!$this->relationLoaded('post') || !$this->post) {
            return $default;
        }

        return $this->post->getMeta($key, $default);
    }


    public function getImageUrl()
    {
        $imageUrl = $this->getMetaValue('_event_image');
        if ($imageUrl) {
            return $imageUrl;
        }

        $thumbnailId = \DB::connection('wordpress')
        ->table('ism13qf_postmeta')
        ->where('post_id', $this->post_id)
        ->where('meta_key', '_thumbnail_id')
        ->value('meta_value');
        
        if ($thumbnailId) {
            $imageUrl = \DB::connection('wordpress')
                ->table('ism13qf_posts')
                ->where('ID', $thumbnailId)
                ->value('guid');
            return $imageUrl ?: asset('assets/img/placeholder.png');
        }
    }
}