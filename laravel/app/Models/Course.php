<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'id',
        'title',
        'level',
        'duration',
        'description',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
