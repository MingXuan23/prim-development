<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nama'         =>  'required',
            'telno'        =>  'required|numeric',
            'email'        =>  ['required', 'email', \Illuminate\Validation\Rule::unique('organizations')->ignore($this->id)],
            'address'      =>  'required',
            'postcode'     =>  'required',
            'district'     =>  'required',
            'state'        =>  'required',
            'type_org'     =>  'required',
            'city'         =>  'required',
            'parent_org'   =>  'nullable',
        ];
    }

    // public function rules()
    // {
    //     return [
    //         'nama'         =>  'required',
    //         // 'code'         =>  'required',
    //         'telno'        =>  'required|numeric|digits_between:10,11',
    //         'email'        =>  'required|email|unique:organizations',
    //         'address'      =>  'required',
    //         'postcode'     =>  'required',
    //         'state'        =>  'required',
    //         'type_org'     =>  'required',
    //         'fixed_charges'      =>  'required|numeric',

    //     ];
    // }

    // public function validated()
    // {
    //     return array_merge(parent::validated(), [
    //         'telno' => '+6'.$this->input('telno'),
    //         'code' => 'MS' . str_pad($this->route('organization.store'), 5, "0", STR_PAD_LEFT)
    //     ]);
    // }
}
