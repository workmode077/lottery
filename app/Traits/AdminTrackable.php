<?php

namespace App\Traits;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

trait AdminTrackable
{
    protected static function bootAdminTrackable()
    {
        static::creating(fn($model) => $model->created_by = Auth::guard('admin')->id());
        static::updating(fn($model) => $model->updated_by = Auth::guard('admin')->id());
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
