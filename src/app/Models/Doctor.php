<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
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
		'birthday',
		'sex',
		'crm',
		'schedules',
	];

	protected $hidden = [
		'address_id',
	];

	protected $casts = [
		'schedules' => 'array',
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
