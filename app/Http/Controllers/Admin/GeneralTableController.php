<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GeneralTableService;

class GeneralTableController extends Controller
{
	private $generalTableService;

	public function __construct(GeneralTableService $generalTableService)
	{
		$this->generalTableService = $generalTableService;
	}

	public function index()
	{
		$data = $this->generalTableService->index();

		return view('pages.admin.general_table.index')->with(compact(['data',]));
	}
}
