<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DoctorRequest;
use App\Services\AddressService;
use App\Services\DoctorService;

class DoctorController extends Controller
{
	private $doctorService, $addressService;

	public function __construct(DoctorService $doctorService, AddressService $addressService)
	{
		$this->doctorService = $doctorService;
		$this->addressService = $addressService;
	}

	public function index()
	{
		$doctors = $this->doctorService->index();

		return view('pages.admin.doctors.index')->with(compact(['doctors',]));
	}

	public function create()
	{
		$doctor = null;

		return view('pages.admin.doctors.create')->with(compact(['doctor']));
	}

	public function store(DoctorRequest $request)
	{
		$request->validated();

		$address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')), strtoupper(request('street')), strtoupper(request('neighborhood')));

		if (!$address) {
			$address = $this->addressService->store();
		}

		request()->merge(['address_id' => $address->id]);

		$this->doctorService->store();

		$this->doctorService->flashSuccessStore();

		return redirect()->route('admin.doctors.index');
	}

	public function edit($id)
	{
		$doctor = $this->doctorService->show($id);

		if (!$doctor) {
			$this->doctorService->flashNotFound();
			return redirect()->route('admin.doctors.index');
		}

		return view('pages.admin.doctors.edit')->with(compact(['doctor']));
	}

	public function update(DoctorRequest $request, $id)
	{
		$request->validated();

		$doctor = $this->doctorService->show($id);

		if (!$doctor) {
			$this->doctorService->flashNotFound();
			return redirect()->route('admin.doctors.index');
		}

		$address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')),strtoupper(request('street')),strtoupper(request('neighborhood')));

		if (!$address) {
			$address = $this->addressService->store();
		}

		request()->merge(['address_id' => $address->id]);

		$this->doctorService->update($id);

		$this->doctorService->flashSuccessUpdate();
		return redirect()->route('admin.doctors.index');
	}

	public function destroy($id)
	{
		!$this->doctorService->destroy($id) ? $this->doctorService->flashNotFound() : $this->doctorService->flashSuccessDestroy();

		return redirect()->route('admin.doctors.index');
	}

	public function filter()
	{
		return response()->json($this->doctorService->filter());
	}
}
