<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class Company extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'social_name'=>'required |min:5|max:191',
            'alias_name'=> 'required',
            'document_company'=> 'required',
            'document_company_secondary'=> 'required',

            // Adrdess  ENDEREÇO
            'zipcode'=>'required|min:8|max:9',
            'street'=>'required',
            'number'=>'required',
            'neighborhood'=>'required',
            'number'=>'required',
            'state'=>'required',
            'city'=>'required',
        ];
    }
}
