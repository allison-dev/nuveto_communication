<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 * @return array
	 */
	public function rules()
	{
		switch ($this->method()) {
			case 'POST':
				return [
					'name'			=> 'required|max:255',
					'email' 		=> 'email|max:200|unique:patients',
					'cpf'      		=> 'required|formato_cpf|cpf',
					'postcode' 		=> 'required|max:10',
				];
				break;
			case 'PUT':
				return [
					'name'			=> 'required|max:255',
					'email' 		=> 'email|max:200|unique:patients,email,' . $this->id,
					'cpf'      		=> 'required|formato_cpf|cpf',
					'postcode' 		=> 'required|max:10',
				];
				break;
			default:
				break;
		}
	}
}
