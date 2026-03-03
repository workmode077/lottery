<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Game;
use App\Models\User;
use App\Models\BillItem;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $modelClass = 'Bill';


    // Bill belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //  Bill belongs to a Game
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    // Bill has many Bill Items
    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function billItems()
    {
        return $this->hasMany(BillItem::class);
    }
}
