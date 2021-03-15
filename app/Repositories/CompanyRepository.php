<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Common\BaseRepository;

class CompanyRepository extends BaseRepository
{
	public function model()
	{
		return Company::class;
	}

	public function filter()
	{
		return Company::whereRaw('LOWER(name) LIKE "%' . strtolower(request()->name) . '%"')
			->get();
	}
}
