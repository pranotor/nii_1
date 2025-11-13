<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class StoreItem extends FormRequest
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
        $kode_perk = $request->input('formdata.kode_perk');
        $kode_barang = $request->input('formdata.kode_perk');
        $id = $request->input('formdata.prevReferensi');
        //dd($request->input('formdata.kode_perk'));
        return [
            'formdata.kode_perk' => ['required'],
            'formdata.kode_barang' => ['required','unique:t_item,kode_barang,'.$id.',id,kode_perk,'.$kode_perk],
            'formdata.uraian' => ['required'],
            'formdata.satuan' => ['required'],
        ];
    }
    public function messages()
    {
        return [
            'formdata.kode_perk.required' => 'Kode Perkiraan belum terisi...',
            'formdata.kode_barang.required'  => 'Kode Barang belum terisi...',
            'formdata.kode_barang.unique'  => 'Kode Barang sudah pernah digunakan, gunakan kode yang lain...',
            'formdata.uraian.required'  => 'Uraian belum terisi...',
            'formdata.satuan.required'  => 'Satuan belum terisi...',

        ];
    }
}
