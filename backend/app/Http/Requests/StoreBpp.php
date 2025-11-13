<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreBpp extends FormRequest
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
            'formdata.tanggal' => ['required', 'date'],
            'formdata.referensi' => ['required','string','unique:t_minta,bpp_no,'.$id.',bpp_no'],
            'formdata.datatrans' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.tanggal.required' => 'Tanggal belum terisi...',
            'formdata.referensi.required'  => 'Referensi belum terisi...',
            'formdata.referensi.unique'  => 'Nomor BPP sudah pernah digunakan, gunakan nomor yang lain...',
            'formdata.datatrans.required'  => 'Data Pengeluaran Barang belum diinput...',

        ];
    }
}
