<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'username' => 'required|string|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
            'foto' => 'required|mimes:jpg,jpeg,png',
            'role' => 'required|numeric',
        ];
    }
}
