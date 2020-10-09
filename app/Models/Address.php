<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
	use HasFactory;

	protected $fillable = [
		'street',
		'neighborhood',
		'city',
		'uf',
		'postcode',
	];

	public function patients()
	{
		return $this->hasMany(Patient::class);
	}

	public function doctors()
	{
		return $this->hasMany(Doctor::class);
	}
}
