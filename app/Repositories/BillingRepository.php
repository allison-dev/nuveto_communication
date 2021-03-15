<?php

namespace App\Repositories;

use App\Models\Billings;
use App\Repositories\Common\BaseRepository;

class BillingRepository extends BaseRepository
{
	public function model()
	{
		return Billings::class;
	}

	public function filter()
	{
		return Billings::whereRaw('LOWER(network) LIKE "%' . strtolower(request()->name) . '%"')
			->get();
	}
}
