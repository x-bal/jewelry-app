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
            'kode_barang' => 'required|string',
            'nama_barang' => 'required|string',
            'locator' => 'required|numeric',
            'satuan' => 'required|string',
            'tipe' => 'required|numeric',
            'subtipe' => 'required|numeric',
            'harga' => 'required|numeric',
            'berat' => 'required|numeric',
            // 'foto' => 'nullable|mimes:jpg,jpeg,png'
        ];
    }
}
