<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = ['language_id', 'module', 'key', 'value'];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
