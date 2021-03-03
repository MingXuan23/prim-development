<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonationRequest extends FormRequest
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
            'organization'  =>  'required|not_in:0',
            'nama'          =>  'required',
            'description'   =>  'required',
            'date_started'  =>  'required',
            'date_end'      =>  'required'
        ];
    }
}
