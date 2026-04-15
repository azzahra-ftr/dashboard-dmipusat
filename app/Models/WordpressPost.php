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
        'post_status',
        'post_type',
        'post_author',
        'post_date',
        'post_name'
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
}