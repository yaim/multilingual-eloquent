<?php

namespace Yaim\MultilingualEloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Yaim\MultilingualEloquent\Database\Eloquent\MultilingualModel;

class LanguageScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $this->applyOnMultilingualModel($builder, $model);
    }

    private function applyOnMultilingualModel(Builder $builder, MultilingualModel $model)
    {
        $builder
        ->leftJoin($model->getTranslationTable(), function ($join) use ($model) {
            $join->on($model->getQualifiedKeyName(), '=', $model->getQualifiedTranslationForeignKeyName())
                 ->where($model->getQualifiedLanguageCodeName(), '=', $model->getLocale());
        })
        ->select($model->getTable().'.*', ...$model->getTranslatable());
    }
}
