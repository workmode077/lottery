<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGame extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $modelClass = 'UserGame';

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
