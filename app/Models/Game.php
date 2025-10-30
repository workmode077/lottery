<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $modelClass = 'Game';

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function resultEntries()
    {
        return $this->hasMany(ResultEntry::class, 'game_id');
    }
}
