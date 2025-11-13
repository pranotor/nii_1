<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduksi extends FormRequest
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
            'formdata.datatransprod' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.tanggal.required' => 'Tanggal belum terisi...',
            'formdata.referensi.required'  => 'Referensi belum terisi...',
            'formdata.datatransprod.required'  => 'Data Bahan Baku belum diinput...',

        ];
    }
}
