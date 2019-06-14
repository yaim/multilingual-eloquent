Laravel Multilingual Eloquent Model
====================

[![Build Status](https://travis-ci.org/yaim/multilingual-eloquent.svg?branch=master)](https://travis-ci.org/yaim/multilingual-eloquent)
[![Code Coverage](https://codecov.io/gh/yaim/multilingual-eloquent/branch/master/graph/badge.svg)](https://codecov.io/gh/yaim/multilingual-eloquent)
[![License](https://poser.pugx.org/yaim/multilingual-eloquent/license.svg)](https://packagist.org/packages/yaim/multilingual-eloquent)
[![StyleCI](https://github.styleci.io/repos/191971433/shield)](https://github.styleci.io/repos/191971433)

Multilingual Eloquent is a simple to setup and use Laravel package for storing dynamic translated (user provided) content in eloquent models.

### Docs

* [Sample](#sample)
* [Setup](#setup)
* [Customization](#customization)

## Sample

**Getting translated attributes**

```php
  $author = Author::first();
  echo $author->email; // sun.tzu@example.com
  echo $author->name; // Sun Tzu

  $author = Author::locale('zh_Hans')->first();
  echo $author->email; // sun.tzu@example.com
  echo $author->name; // 孫子
```

**Creating translated models**

```php
  $author = Author::create([
    'email' => 'sun.tzu@example.com',
    'name' => 'Sun Tzu',
  ]);

  echo $author->name; // Sun Tzu
  
  $author->setLocale('zh_Hans')->update([
      'name' => '孫子',
  ]);

  $author = Author::locale('zh_Hans')->first();
  echo $author->name; // 孫子
```

## Setup

### Step 1: Installing Package

Add the package in your composer.json by executing the command.

```bash
composer require yaim/multilingual-eloquent
```

### Step 2: Migrating Database

In this example, we want to translate the model `Author`. We will need an extra table `author_translations`:

```php
Schema::create('authors', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('email');
    $table->timestamps();
});

Schema::create('author_translations', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedBigInteger('author_id');
    $table->string('language_code');
    $table->string('name');
    $table->text('bio');
    $table->timestamps();

    $table->foreign('author_id')
          ->references('id')->on('authors');
});
```

### Step 3: Extending MultilingualEloquent Model

1. The multilingual model `Author` should extends `Yaim\MultilingualEloquent\Database\Eloquent\MultilingualModel`.
2. The protected `translatable` property should list translatable fields.
3. The if you want to mass assign translatable field you must include it inside `fillable` property.


```php
// app/Author.php

use Yaim\MultilingualEloquent\Database\Eloquent\MultilingualModel;

class Author extends MultilingualModel {
    
    protected $fillable = [
        'email',
        'name',
        'bio',
    ];

    protected $translatable = [
        'name',
        'bio',
    ];
}
```

## Customization

You can customize Multilingual Eloquent default naming convention for the following name:

```php
// app/Post.php

use Yaim\MultilingualEloquent\Database\Eloquent\MultilingualModel;

class Post extends MultilingualModel
{
    // default translations table name => 'post_translations'
    protected $translationTable = 'multilingual_posts';

    // default translation foreign key name => 'post_id'
    protected $translationForeignKey = 'multilingual_post_id';

    // default language code key name => 'language_code'
    protected $languageCode = 'translation_language_code';

    protected $fillable = [
        'title',
        'content',
    ];

    protected $translatable = [
        'title',
        'content',
    ];
}
```
