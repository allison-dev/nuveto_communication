<?php

namespace App\Repositories;

use App\Models\ConversationSession;
use App\Repositories\Common\BaseRepository;

class GeneralTableRepository extends BaseRepository
{
	public function model()
	{
		return ConversationSession::class;
	}
}
