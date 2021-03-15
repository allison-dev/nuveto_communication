<?php

namespace App\Repositories;

use App\Models\Invoices;
use App\Repositories\Common\BaseRepository;

class InvoicesRepository extends BaseRepository
{
	public function model()
	{
		return Invoices::class;
	}
}
