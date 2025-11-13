<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAktiva extends FormRequest
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
            'formdata.kode' => ['required'],
            'formdata.gol' => ['required'],
            'formdata.jumlah' => ['required'],
            'formdata.harga_unit' => ['required'],
            ''
        ];
    }
    public function messages()
    {
        return [
            'formdata.tanggal.required' => 'Tanggal belum terisi...',
            'formdata.kode.required'  => 'Kelompok Aktiva belum terisi...',
            'formdata.gol.required'  => 'Golongan Tarif Aktiva belum dipilih...',
        ];
    }
}
