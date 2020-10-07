<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
					'name'     => 'required|unique:users',
					'email'    => 'required|email|unique:users',
					'password' => 'required|confirmed',
				];
				break;
			case 'PUT':
				return [
					'name'     => 'required|unique:users',
					'email'    => 'required|email|unique:users' . $this->id,
					'password' => 'required|confirmed',
				];
				break;
			default:
				break;
		}
	}
}
