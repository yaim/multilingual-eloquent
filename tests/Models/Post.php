<?php

namespace Tests\Models;

use Yaim\MultilingualEloquent\Database\Eloquent\MultilingualModel;

class Post extends MultilingualModel
{
    protected $translationTable = 'multilingual_posts';
    protected $translationForeignKey = 'multilingual_post_id';
    protected $languageCode = 'translation_language_code';

    protected $fillable = [
        'title',
        'content',
    ];

    protected $translatable = [
        'title',
        'content',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
