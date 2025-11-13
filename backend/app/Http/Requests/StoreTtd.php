<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTtd extends FormRequest
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
            'formdata.module_id' => ['required'],
            'formdata.datatrans' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.module_id.required' => 'Module belum terisi...',
            'formdata.datatrans.required'  => 'Data pejabat penandatangan masih kosong...',

        ];
    }
}
