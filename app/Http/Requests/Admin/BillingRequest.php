<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BillingRequest extends FormRequest
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
					'network'		=> 'required|max:255',
					'sessions' 		=> 'required|max:200',
				];
				break;
			case 'PUT':
				return [
					'network'		=> 'required|max:255',
					'sessions' 		=> 'required|max:200',
				];
				break;
			default:
				break;
		}
	}
}
