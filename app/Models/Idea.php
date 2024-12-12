<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Idea extends Model
{
    protected $table = 'ideas';
    protected $fillable = [

    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class,'idea_user');
    }
}
