<?php

namespace App\Http\Controllers\Api\v1\Billing;

use App\Http\Controllers\Api\BaseController;
use App\Services\AddressService;
use App\Services\CompanyService;

class BillingController extends BaseController
{
    private $companyService, $addressService;

    public function __construct(CompanyService $companyService, AddressService $addressService)
    {
        $this->companyService = $companyService;
        $this->addressService = $addressService;
    }

    public function index()
    {
        $verify_code = sha1('Nuveto Sigma Verify');
        if ($verify_code == request('sigma_id')) {

            $address = $this->addressService->showByStreet(preg_replace('/\D/', '', request('postcode')), strtoupper(request('street')), strtoupper(request('neighborhood')));

            if (!$address) {
                $address = $this->addressService->store();
            }

            request()->merge(['address_id' => $address->id]);

            $this->companyService->store();

            $this->companyService->flashSuccessStore();

            return redirect()->away(request('redirect'));
        }
    }
}
