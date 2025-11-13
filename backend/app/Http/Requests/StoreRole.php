<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreRole extends FormRequest
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
    public function rules(Request $request)
    {
        $id = $request->input('formdata.prevReferensi');
        return [
            'formdata.role_name' => ['required'],
            'formdata.datatrans' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.role_name.required' => 'Nama Role belum terisi...',
            'formdata.datatrans.required'  => 'Wewenang menu belum terisi...',
        ];
    }
}
