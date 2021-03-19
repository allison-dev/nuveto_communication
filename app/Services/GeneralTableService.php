<?php

namespace App\Services;

use App\Repositories\GeneralTableRepository;

class GeneralTableService implements GeneralTableServiceInterface
{
	private $generalTableRepository;

	public function __construct(GeneralTableRepository $generalTableRepository)
	{
		$this->generalTableRepository = $generalTableRepository;
	}

	public function index()
	{
        $data = $this->generalTableRepository->orderBy('created_at','desc')->get();

		if (!count($data)) {
			return false;
		}

		return $data;
	}
}
