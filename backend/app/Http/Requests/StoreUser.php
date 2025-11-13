<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreUser extends FormRequest
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
            'formdata.email' => ['required','unique:users,email,'.$id.',id'],
            'formdata.name' => ['required'],
            'formdata.password' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'formdata.email.required' => 'User ID belum terisi...',
            'formdata.email.unique'  => 'User ID sudah pernah digunakan, gunakan USer ID yang lain...',
            'formdata.nama.required'  => 'Nama belum terisi...',
            'formdata.password.required'  => 'Password belum terisi...',

        ];
    }
}
