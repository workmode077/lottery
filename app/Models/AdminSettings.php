<?php

namespace App\Models;

use App\Traits\AdminTrackable;
use App\Traits\MediaUpload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class AdminSettings extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, MediaUpload, AdminTrackable;

    protected $fillable = ['value'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('value')->singleFile();
    }

    /**
     * Get the formatted key as a title-cased string with spaces.
     *
     * @return string
     */
    public function getKeyValueAttribute()
    {
        return str_replace('-', ' ', Str::title($this->key));
    }

    /**
     * Get the formatted value based on type.
     *
     * @return string|null
     */
    public function getFormattedValueAttribute()
    {
        if ($this->type == 2) {
            return $this->hasMedia('value')
                ? $this->getFirstMediaUrl('value')
                : ($this->value && file_exists($this->value) ? asset($this->value) : null);
        }

        return $this->type == 1 ? $this->value : null;
    }
}
