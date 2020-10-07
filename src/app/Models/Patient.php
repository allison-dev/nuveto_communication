<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
	use HasFactory;

	protected $attributes = [
		'sex' => 'F',
	];

	protected $fillable = [
		'address_id',
		'number_home',
		'complement',
		'name',
		'email',
		'cellphone',
		'cpf',
		'birth',
		'sex',
	];

	protected $hidden = [
		'address_id',
	];

	public function address()
	{
		return $this->belongsTo(Address::class);
	}

	public function schedules()
	{
		return $this->hasMany(Scheduling::class);
	}
}
