<?php

namespace App\Repositories;

use App\Models\WhatsappConversation;
use App\Repositories\Common\BaseRepository;

class MediaRepository extends BaseRepository
{
	public function model()
	{
		return WhatsappConversation::class;
	}
}
