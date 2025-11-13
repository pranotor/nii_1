<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StorePerkiraan extends FormRequest
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
            'formdata.kode_perk' => ['required','unique:rekening,kode_perk,'.$id.',kode_perk'],
            'formdata.nama_perk' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.kode_perk.required' => 'Kode Perkiraan belum terisi...',
            'formdata.kode_perk.unique'  => 'Kode Perkiraan sudah pernah digunakan, gunakan Kode yang lain...',
            'formdata.nama_perk.required'  => 'Nama Perkiraan belum terisi...',

        ];
    }
}
