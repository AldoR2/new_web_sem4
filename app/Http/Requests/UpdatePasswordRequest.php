<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];
    }

    public function messages(){
        return [
            'current_password.required' => 'Password saat ini harus diisi',
            'current_password.current_password' => 'Password yang Anda masukkan tidak valid',
            'password.required' => 'Harap masukkan password baru',
            'password.confirmed' => 'Konfirmasi Password tidak sesuai',

        ];
    }
}
