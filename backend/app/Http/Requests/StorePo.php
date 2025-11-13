<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePo extends FormRequest
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
            'formdata.tanggal' => ['required', 'date'],
            'formdata.referensi' => ['required','string'],
            'formdata.datatrans' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.tanggal.required' => 'Tanggal belum terisi...',
            'formdata.referensi.required'  => 'Referensi belum terisi...',
            'formdata.datatrans.required'  => 'Data Penerimaan Barang belum diinput...',

        ];
    }
}
