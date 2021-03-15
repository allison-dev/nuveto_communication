<?php

namespace App\Services;

use App\Repositories\AddressRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddressService implements AddressServiceInterface
{
	private $addressRepository;
	protected $baseUrl = '';
	protected $header = [];

	public function __construct(AddressRepository $addressRepository)
	{
		$this->addressRepository = $addressRepository;
		$this->header = [
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		];

		$this->baseUrl = 'https://api.postmon.com.br/v1/cep/';
	}

	public function showPostcode()
	{
		$address = postomonAPI($this->header, $this->baseUrl . request('postcode'));

		if (isset($address['success']) && !$address['success']) {
			return false;
		}

		return $address;
	}

	public function showByStreet($postcode, $street, $neighborhood)
	{
		try {

			return $this->addressRepository->where('postcode', $postcode)->where('street', '%' . $street . '%', 'LIKE')->where('neighborhood', '%' . $neighborhood . '%', 'LIKE')->first();
		} catch (ModelNotFoundException $e) {
			return false;
		}
	}

	public function showByWhere($wheres = [])
	{
		$query = $this->addressRepository;

		if (count($wheres)) {
			foreach ($wheres as $where) {
				$query->where($where['key'], $where['value']);
			}
		}
		try {
			return $query->first();
		} catch (ModelNotFoundException $e) {
			return false;
		}
	}

	public function store()
	{
		return $this->addressRepository->create($this->values());
	}

	public function update($addressId)
	{
		return $this->addressRepository->updateById($addressId, $this->values());
	}

	private function values()
	{
		return [
			'street'       => !is_null(request('street')) ? request('street') : null,
			'neighborhood' => !is_null(request('neighborhood')) ? request('neighborhood') : null,
			'city'         => !is_null(request('city')) ? request('city') : null,
			'uf'           => !is_null(request('uf')) ? request('uf') : null,
			'postcode'     => preg_replace('/\D/', '', request('postcode')),
		];
	}
}
