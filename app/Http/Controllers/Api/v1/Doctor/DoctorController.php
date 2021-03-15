<?php

namespace App\Http\Controllers\Api\v1\Doctor;

use App\Http\Controllers\Api\BaseController;
use App\Services\DoctorService;

class DoctorController extends BaseController
{
	private $doctorService;

	public function __construct(DoctorService $doctorService)
	{
		$this->doctorService = $doctorService;
	}

	public function index()
	{
		$doctors = $this->doctorService->index();

		dd($doctors);

		if (!$doctors) {
			return $this->responseError(trans('system.not_founds_m', ['value'=> trans('system.companies'),]));
		}

		return $this->responseSuccess($doctors);
	}
}
