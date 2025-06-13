<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreMasterRuangan extends FormRequest
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
    public function rules($id = null): array
    {

        $id = $id ?? $this->route('master_ruangan');

        return [
            'nama_ruangan' => ['required','max:150',Rule::unique('ruangans', 'nama_ruangan')->ignore($id),],
        ];
    }

    public function messages(){
        return [
            'nama_ruangan.required' => 'Nama Ruangan tidak boleh kosong',
            'nama_ruangan.max' => 'Nama Ruangan Maksimal 150 karakter',
            'nama_ruangan.unique' => 'Nama Ruangan Sudah terdaftar',
        ];
    }
}
