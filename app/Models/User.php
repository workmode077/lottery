<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $modelClass = 'User';

     public function scopeActive($query)
    {
        return $query->where('status', true);
    }

   
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    public function setPlainPasswordAttribute($value)
    {
        $this->attributes['plain_password'] = $value;
        $this->attributes['password']       = Hash::make($value);
    }

    
    // Sub-agent (User)
    public function agent()
    {
        return $this->belongsTo(User::class, 'parent_id')
                    ->where('user_type', 'agent');
    }

    // Agent (User)
    public function superAgent()
    {
        return $this->belongsTo(User::class, 'parent_id')
                    ->where('user_type', 'super_agent');
    }
}
