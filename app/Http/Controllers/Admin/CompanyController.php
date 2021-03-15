<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyRequest;
use App\Services\AddressService;
use App\Services\CompanyService;

class CompanyController extends Controller
{
	private $companyService, $addressService;

	public function __construct(CompanyService $companyService, AddressService $addressService)
	{
		$this->companyService = $companyService;
		$this->addressService = $addressService;
	}

	public function index()
	{
		$companies = $this->companyService->index();

		return view('pages.admin.company.index')->with(compact(['companies',]));
	}

	public function create()
	{
		$company = null;

		return view('pages.admin.company.create')->with(compact(['company']));
	}

	public function store(CompanyRequest $request)
	{
		$request->validated();

		$address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')), strtoupper(request('street')), strtoupper(request('neighborhood')));

		if (!$address) {
			$address = $this->addressService->store();
		}

		request()->merge(['address_id' => $address->id]);

		$this->companyService->store();

		$this->companyService->flashSuccessStore();

		return redirect()->route('admin.company.index');
	}

	public function edit($id)
	{
		$company = $this->companyService->show($id);

		if (!$company) {
			$this->companyService->flashNotFound();
			return redirect()->route('admin.company.index');
		}

		return view('pages.admin.company.edit')->with(compact(['company']));
	}

	public function update(CompanyRequest $request, $id)
	{
		$request->validated();

		$company = $this->companyService->show($id);

		if (!$company) {
			$this->companyService->flashNotFound();
			return redirect()->route('admin.company.index');
		}

		$address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')),strtoupper(request('street')),strtoupper(request('neighborhood')));

		if (!$address) {
			$address = $this->addressService->store();
		}

		request()->merge(['address_id' => $address->id]);

		$this->companyService->update($id);

		$this->companyService->flashSuccessUpdate();
		return redirect()->route('admin.company.index');
	}

	public function destroy($id)
	{
		!$this->companyService->destroy($id) ? $this->companyService->flashNotFound() : $this->companyService->flashSuccessDestroy();

		return redirect()->route('admin.company.index');
	}

	public function filter()
	{
		return response()->json($this->companyService->filter());
	}
}
