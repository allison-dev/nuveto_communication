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
                    'network'        => 'required|unique:billings',
                    'sessions'         => 'required',
                    'price'         => 'required',
                ];
                break;
            case 'PUT':
                return [
                    'network'        => 'required|unique:billings,id,' . $this->id,
                    'sessions'         => 'required',
                    'price'         => 'required',
                ];
                break;
            default:
                break;
        }
    }
}
