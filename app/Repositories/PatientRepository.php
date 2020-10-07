<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Repositories\Common\BaseRepository;

class PatientRepository extends BaseRepository
{
	public function model()
	{
		return Patient::class;
	}

	public function filter()
	{
		return Patient::whereRaw('LOWER(name) LIKE "%' . strtolower(request()->name) . '%"')
			->get();
	}
}
