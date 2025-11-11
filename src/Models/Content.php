<?php

namespace AwStudio\Contentable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    /** @use HasFactory<\Database\Factories\ContentFactory> */
    use HasFactory;

    //  table name is content
    protected $table = 'content';

    protected $fillable = [
        'contentable_type',
        'contentable_id',
        'key',
        'type',
        'content',
        'order',
    ];

    protected $casts = [
        'content' => 'json',
    ];

    public function contentable()
    {
        return $this->morphTo();
    }
}
