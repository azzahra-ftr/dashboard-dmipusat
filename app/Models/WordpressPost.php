<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WordpressPost extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'ism13qf_posts';  
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
    'post_title',
    'post_content',
    'post_excerpt',
    'post_status',
    'post_type',
    'post_author',
    'post_date',
    'post_date_gmt',
    'post_modified',
    'post_modified_gmt',
    'post_name',
    'guid',
    'to_ping',
    'pinged',
    'post_content_filtered',
];

    public function author()
{
    return $this->belongsTo(
        \App\Models\WordpressUser::class,
        'post_author',
        'ID'
    );
}

public function getImageUrl()
{
    $thumbnailId = \DB::connection($this->connection)
        ->table('ism13qf_postmeta')
        ->where('post_id', $this->ID)
        ->where('meta_key', '_thumbnail_id')
        ->first();

    if ($thumbnailId) {
        $attachment = \DB::connection($this->connection)
            ->table('ism13qf_posts')
            ->where('ID', $thumbnailId->meta_value)
            ->first();
            
        if ($attachment) {
            return $attachment->guid; 
        }
    }

    return null; 
}

public function meta()
    {
        return $this->hasMany(WordpressPostMeta::class, 'post_id', 'ID');
    }

    public function getMeta($key, $default = null)
    {
        $meta = $this->meta->firstWhere('meta_key', $key);
        return $meta ? $meta->meta_value : $default;
    }

    public function setMeta($key, $value)
    {
        $this->meta()->updateOrCreate(
            ['meta_key' => $key],
            ['meta_value' => $value]
        );
    }
}