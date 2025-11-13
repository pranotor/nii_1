<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StorePegawai extends FormRequest
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
            'formdata.nik' => ['required','unique:t_pegawai,nik,'.$id.',nik'],
            'formdata.nama' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.nik.required' => 'NIK belum terisi...',
            'formdata.nik.unique'  => 'NIK sudah pernah digunakan, gunakan NIK yang lain...',
            'formdata.nama.required'  => 'Nama belum terisi...',

        ];
    }
}
