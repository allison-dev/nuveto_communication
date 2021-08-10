<?php

namespace App\Repositories;

use App\Models\Medias;
use App\Repositories\Common\BaseRepository;

class MediaRepository extends BaseRepository
{
	public function model()
	{
		return Medias::class;
	}
}
