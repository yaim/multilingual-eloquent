<?php

namespace Yaim\MultilingualEloquent\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yaim\MultilingualEloquent\Scopes\LanguageScope;

abstract class MultilingualModel extends Model
{
    protected $locale;
    protected $translationTable;
    protected $translationForeignKey;
    protected $languageCode;
    protected $translatable = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new LanguageScope());
    }

    public static function locale(string $locale)
    {
        return (new static())->setLocale($locale);
    }

    protected function extractTranslatableDirty()
    {
        $dirty = $this->getTranslatableDirty();

        foreach ($dirty as $key => $value) {
            unset($this->attributes[$key]);
        }

        return $dirty;
    }

    protected function performInsert(Builder $query)
    {
        $dirty = $this->extractTranslatableDirty();

        parent::performInsert($query);

        return $this->updateOrInsertTranslation($dirty);
    }

    protected function performUpdate(Builder $query)
    {
        $dirty = $this->extractTranslatableDirty();

        parent::performUpdate($query);

        return $this->updateOrInsertTranslation($dirty);
    }

    protected function performDeleteOnModel()
    {
        DB::table($this->getTranslationTable())
            ->where($this->getTranslationForeignKeyName(), $this->getKey())
            ->delete();

        parent::performDeleteOnModel();
    }

    private function updateOrInsertTranslation(array $attributes)
    {
        if (empty($attributes)) {
            return true;
        }

        DB::table($this->getTranslationTable())->updateOrInsert([
            $this->getLanguageCodeName()          => $this->getLocale(),
            $this->getTranslationForeignKeyName() => $this->getKey(),
        ], $attributes);

        return true;
    }

    public function getTranslatableDirty()
    {
        $dirty = [];

        foreach ($this->translatable as $field) {
            $dirty[$field] = isset($this->getDirty()[$field]) ? $this->getDirty()[$field] : null;
        }

        return array_filter($dirty);
    }

    public function getTranslationTable()
    {
        if (isset($this->translationTable)) {
            return $this->translationTable;
        }

        return Str::snake(class_basename($this)).'_translations';
    }

    public function getLanguageCodeName()
    {
        if (isset($this->languageCode)) {
            return $this->languageCode;
        }

        return 'language_code';
    }

    public function getTranslationForeignKeyName()
    {
        if (isset($this->translationForeignKey)) {
            return $this->translationForeignKey;
        }

        return $this->getForeignKey();
    }

    public function getLocale()
    {
        $locale = config('app.locale');

        if (isset($this->locale)) {
            $locale = $this->locale;
        } elseif (isset(request()->getLanguages()[0])) {
            $locale = request()->getLanguages()[0];
        }

        return $locale;
    }

    public function setLocale(string $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTranslatable()
    {
        return $this->translatable;
    }

    public function getQualifiedTranslationForeignKeyName()
    {
        return $this->getTranslationTable().'.'.$this->getTranslationForeignKeyName();
    }

    public function getQualifiedLanguageCodeName()
    {
        return $this->getTranslationTable().'.'.$this->getLanguageCodeName();
    }

    public function availableTranslatedLanguages()
    {
        return DB::table($this->getTranslationTable())
            ->where($this->getTranslationForeignKeyName(), $this->getKey())
            ->pluck($this->getLanguageCodeName());
    }
}
