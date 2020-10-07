<?php

namespace App\Repositories;

use App\Models\Scheduling;
use App\Repositories\Common\BaseRepository;

class SchedulingRepository extends BaseRepository
{
	public function model()
	{
		return Scheduling::class;
	}
}
