<?php

namespace Tests\Models;

use Yaim\MultilingualEloquent\Database\Eloquent\MultilingualModel;

class Author extends MultilingualModel
{
    protected $fillable = [
        'email',
        'name',
        'bio',
    ];

    protected $translatable = [
        'name',
        'bio',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
