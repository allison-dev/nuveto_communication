<?php

namespace App\Repositories;

use App\Models\Address;
use App\Repositories\Common\BaseRepository;

class AddressRepository extends BaseRepository
{
	public function model()
	{
		return Address::class;
	}
}
