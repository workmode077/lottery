<?php

namespace App\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Year extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $modelClass = 'Year';

     public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'year'
            ]
        ];
    }
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function modelYears()
    {
        return $this->hasMany(ModelYear::class);
    }

}
