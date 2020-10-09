<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SchedulingRequest;
use App\Services\SchedulingService;

class SchedulingController extends Controller
{
	private $schedulingService;

	public function __construct(SchedulingService $schedulingService)
	{
		$this->schedulingService = $schedulingService;
	}

	public function index()
	{
		$schedules = $this->schedulingService->index();

		return view('pages.admin.schedules.index')->with(compact(['schedules']));
	}

	public function create()
	{
		$scheduling = null;

		return view('pages.admin.schedules.create')->with(compact(['scheduling']));
	}

	public function store(SchedulingRequest $request)
	{
		$request->validated();

		$this->schedulingService->store();

		$this->schedulingService->flashSuccessStore();

		return redirect()->route('admin.schedules.index');
	}

	public function edit($id)
	{
		$scheduling = $this->schedulingService->show($id);

		if (!$scheduling) {
			$this->schedulingService->flashNotFound();
			return redirect()->route('admin.schedules.index');
		}

		return view('pages.admin.schedules.edit')->with(compact(['scheduling']));
	}

	public function update(SchedulingRequest $request, $id)
	{
		$request->validated();

		$scheduling = $this->schedulingService->update($id);

		if (!$scheduling) {
			$this->schedulingService->flashNotFound();

			return redirect()->route('admin.schedules.index');
		}

		$this->schedulingService->flashSuccessUpdate();

		return redirect()->route('admin.schedules.index');
	}

	public function destroy($id)
	{
		!$this->schedulingService->destroy($id) ? $this->schedulingService->flashNotFound() : $this->schedulingService->flashSuccessDestroy();

		return redirect()->route('admin.schedules.index');
	}
}
