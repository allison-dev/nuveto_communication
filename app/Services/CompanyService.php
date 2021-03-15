<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyService implements CompanyServiceInterface
{
	private $companyRepository;

	public function __construct(CompanyRepository $companyRepository)
	{
		$this->companyRepository = $companyRepository;
	}

	public function index()
	{
        $company = $this->companyRepository->orderBy('name')->get();

		if (!count($company)) {
			return false;
		}

		return $company;
	}

	public function store()
	{
		return $this->companyRepository->create($this->values());
	}

	public function show($id)
	{
		try {
			return $this->companyRepository->where('id', $id)->first();
		} catch (ModelNotFoundException $e) {
			return false;
		}
	}

	public function update($id)
	{
		$company = $this->show($id);

		if (!$company) {
			return false;
		}

		$this->companyRepository->updateById($company->id, $this->values());

		return $company;
	}

	public function destroy($id)
	{
		$company = $this->show($id);

		if (!$company) {
			return false;
		}

		return $this->companyRepository->deleteById($company->id);
	}

	public function filter()
	{
		$companies = $this->companyRepository->filter();

		return !is_null($companies) ? $companies : [];
	}

	private function values()
	{
		$values = [
			'name'  		=> request('name'),
			'email' 		=> request('email'),
			'sex'   		=> request('sex'),
			'cpf_cnpj'   		=> preg_replace('/\D/', '', request('cpf_cnpj')),
		];

		if (!is_null(request('cellphone'))) {
			$values['cellphone'] = preg_replace('/\D/', '', request('cellphone'));
		}

		if (!is_null(request('address_id'))) {
			$values['address_id'] = request('address_id');
		}

		if (!is_null(request('birthday'))) {
			$values['birthday'] = request('birthday');
		}

		if (!is_null(request('number_home'))) {
			$values['number_home'] = request('number_home');
		}

		if (!is_null(request('complement'))) {
			$values['complement'] = request('complement');
		}

		return $values;
	}

	public function flashNotFound()
	{
		return flash(trans('system.not_found_m', ['value' => trans('system.company'),]))->error()->important();
	}

	public function flashSuccessStore()
	{
		return flash(trans('system.store_success_m', ['value' => trans('system.company'),]))->success();
	}

	public function flashSuccessUpdate()
	{
		return flash(trans('system.update_success_m', ['value' => trans('system.company'),]))->success()->important();
	}

	public function flashSuccessDestroy()
	{
		return flash(trans('system.destroy_success_m', ['value' => trans('system.company'),]))->success()->important();
	}
}
