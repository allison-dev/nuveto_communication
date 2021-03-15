<?php

namespace App\Services;

use App\Repositories\SchedulingRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SchedulingService implements SchedulingServiceInterface
{
	private $schedulingRepository;

	public function __construct(SchedulingRepository $schedulingRepository)
	{
		$this->schedulingRepository = $schedulingRepository;
	}

	public function index()
	{
		$schedules = $this->schedulingRepository->orderBy('id', 'DESC')->get();

		if (!count($schedules)) {
			return false;
		}

		return $schedules;
	}

	public function store()
	{
		return $this->schedulingRepository->create($this->values());
	}

	public function show($id)
	{
		try {
			return $this->schedulingRepository->where('id', $id)->first();
		} catch (ModelNotFoundException $e) {
			return false;
		}
	}

	public function update($id)
	{
		$scheduling = $this->show($id);

		if (!$scheduling) {
			return false;
		}

		$this->schedulingRepository->updateById($scheduling->id, $this->values());

		return $scheduling;
	}

	public function destroy($id)
	{
		$scheduling = $this->show($id);

		if (!$scheduling) {
			return false;
		}

		return $this->schedulingRepository->deleteById($scheduling->id);
	}

	private function values()
	{
		return [
			'doctor_id'  => request('doctor_id'),
			'patient_id' => request('patient_id'),
			'schedule'   => request('schedule'),
		];
	}

	public function flashNotFound()
	{
		return flash(trans('system.not_found_m', ['value' => trans('system.scheduling'),]))->error()->important();
	}

	public function flashSuccessStore()
	{
		return flash(trans('system.store_success_m', ['value' => trans('system.scheduling'),]))->success();
	}

	public function flashSuccessUpdate()
	{
		return flash(trans('system.update_success_m', ['value' => trans('system.scheduling'),]))->success()->important();
	}

	public function flashSuccessDestroy()
	{
		return flash(trans('system.destroy_success_m', ['value' => trans('system.scheduling'),]))->success()->important();
	}
}
