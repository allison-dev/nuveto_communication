<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BillingRequest;
use App\Services\AddressService;
use App\Services\BillingService;

class BillingController extends Controller
{
	private $billingService, $addressService;

	public function __construct(BillingService $billingService, AddressService $addressService)
	{
		$this->billingService = $billingService;
		$this->addressService = $addressService;
	}

	public function index()
	{
		$billings = $this->billingService->index();

		return view('pages.admin.billing.index')->with(compact(['billings']));
	}

	public function create()
	{
		$billing = null;

		return view('pages.admin.billing.create')->with(compact(['billing']));
	}

	public function store(BillingRequest $request)
	{
		$request->validated();

		$address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')), strtoupper(request('street')), strtoupper(request('neighborhood')));

		if (!$address) {
			$address = $this->addressService->store();
		}

		request()->merge(['address_id' => $address->id]);

		$this->billingService->store();

		$this->billingService->flashSuccessStore();

		return redirect()->route('admin.billings.index');
	}

	public function edit($id)
	{
		$billing = $this->billingService->show($id);

		if (!$billing) {
			$this->billingService->flashNotFound();
			return redirect()->route('admin.billings.index');
		}

		return view('pages.admin.billing.edit')->with(compact(['billing']));
	}

	public function update(BillingRequest $request, $id)
	{
		$request->validated();

		$billing = $this->billingService->show($id);

		if (!$billing) {
			$this->billingService->flashNotFound();

			return redirect()->route('admin.billings.index');
		}

		$address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')), strtoupper(request('street')), strtoupper(request('neighborhood')));

		if (!$address) {
			$address = $this->addressService->store();
		}

		request()->merge(['address_id' => $address->id,]);

		$this->billingService->update($id);

		$this->billingService->flashSuccessUpdate();
		return redirect()->route('admin.billings.index');
	}

	public function destroy($id)
	{
		!$this->billingService->destroy($id) ? $this->billingService->flashNotFound() : $this->billingService->flashSuccessDestroy();

		return redirect()->route('admin.billings.index');
	}

	public function filter()
	{
		return response()->json($this->billingService->filter());
	}
}
