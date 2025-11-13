<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRekanan extends FormRequest
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
            'formdata.kode' => ['required'],
            'formdata.nama' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.kode.required' => 'Kode belum terisi...',
            'formdata.nama.required'  => 'Nama belum terisi...',

        ];
    }
}
