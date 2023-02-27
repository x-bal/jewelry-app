<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangRequest extends FormRequest
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
            'nama_barang' => 'required|string',
            'locator' => 'required|numeric',
            'satuan' => 'required|numeric',
            'tipe' => 'required|numeric',
            'harga' => 'required|numeric',
            'berat' => 'required|numeric',
        ];
    }
}
