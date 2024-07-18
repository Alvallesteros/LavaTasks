<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	use HasFactory, SoftDeletes;

    protected $table = 'tasks';
    protected $fillable = [
        'id',
        'project_id',
        'priority',
        'title',
        'description',
    ];
    protected $appends = [
        'created',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Projects::class);
    }

    public function getCreatedAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
