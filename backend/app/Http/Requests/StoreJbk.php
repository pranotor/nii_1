<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJbk extends FormRequest
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
            'formdata.tgl_bayar' => ['required', 'date'],
            'formdata.referensi' => ['required','string'],
            'formdata.voucher' => ['required','string'],
            'formdata.dataDebet' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.tgl_bayar.required' => 'Tanggal belum terisi...',
            'formdata.referensi.required'  => 'Referensi belum terisi...',
            'formdata.voucher.required'  => 'Voucher belum terisi...',
            'formdata.dataDebet.required'  => 'Data jurnal transaksi belum diinput...',

        ];
    }
}
