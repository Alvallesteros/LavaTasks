<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
	use HasFactory;
	
	protected $table = 'projects';
	public $timestamps = false;
	protected $fillable = [
		'id',
		'name',
	];


	public function tasks(); HasMany
	{
		return $this->HasMany(Task::class);
	}
}
