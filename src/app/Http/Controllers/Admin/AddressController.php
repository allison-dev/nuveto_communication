<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AddressService;

/**
 * Class AddressController
 * @package App\Http\Controllers\Panel
 */
class AddressController extends Controller
{
	private $addressService;

	public function __construct(
		AddressService $addressService
	) {
		$this->addressService = $addressService;
	}

	public function showByPostcode()
	{
		return response()->json($this->addressService->showPostcode());
	}
}
