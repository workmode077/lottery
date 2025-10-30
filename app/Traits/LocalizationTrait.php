<?php
namespace App\Traits;

trait LocalizationTrait
{
    protected function getLocalizedText($model, $fieldPrefix, $default = '', $language = 'en')
    {
        $field = $language === 'ar' ? $fieldPrefix . '_ar' : $fieldPrefix;
        return $model->{$field} ?? $default;
    }
}