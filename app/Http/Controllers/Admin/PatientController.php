<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PatientRequest;
use App\Services\AddressService;
use App\Services\PatientService;

class PatientController extends Controller
{
	private $patientService, $addressService;

	public function __construct(PatientService $patientService, AddressService $addressService)
	{
		$this->patientService = $patientService;
		$this->addressService = $addressService;
	}

	public function index()
	{
		$patients = $this->patientService->index();

		return view('pages.admin.patients.index')->with(compact(['patients']));
	}

	public function create()
	{
		$patient = null;

		return view('pages.admin.patients.create')->with(compact(['patient']));
	}

	public function store(PatientRequest $request)
	{
		$request->validated();

		$address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')), strtoupper(request('street')), strtoupper(request('neighborhood')));

		if (!$address) {
			$address = $this->addressService->store();
		}

		request()->merge(['address_id' => $address->id]);

		$this->patientService->store();

		$this->patientService->flashSuccessStore();

		return redirect()->route('admin.patients.index');
	}

	public function edit($id)
	{
		$patient = $this->patientService->show($id);

		if (!$patient) {
			$this->patientService->flashNotFound();
			return redirect()->route('admin.patients.index');
		}

		return view('pages.admin.patients.edit')->with(compact(['patient']));
	}

	public function update(PatientRequest $request, $id)
	{
		$request->validated();

		$patient = $this->patientService->show($id);

		if (!$patient) {
			$this->patientService->flashNotFound();

			return redirect()->route('admin.patients.index');
		}

		$address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')), strtoupper(request('street')), strtoupper(request('neighborhood')));

		if (!$address) {
			$address = $this->addressService->store();
		}

		request()->merge(['address_id' => $address->id,]);

		$this->patientService->update($id);

		$this->patientService->flashSuccessUpdate();
		return redirect()->route('admin.patients.index');
	}

	public function destroy($id)
	{
		!$this->patientService->destroy($id) ? $this->patientService->flashNotFound() : $this->patientService->flashSuccessDestroy();

		return redirect()->route('admin.patients.index');
	}

	public function filter()
	{
		return response()->json($this->patientService->filter());
	}
}
