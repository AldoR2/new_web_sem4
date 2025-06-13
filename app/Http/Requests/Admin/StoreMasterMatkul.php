<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreMasterMatkul extends FormRequest
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
            'nama_matkul' => 'required|max:100|regex:/^[A-Za-z0-9\s]+$/',
            'prodi_id' => 'required',
            'semester' => 'required',
            'tahun_ajaran_id' => 'required',
            'durasi_matkul' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(){
        return [
            'nama_matkul.required' => 'Mata Kuliah tidak boleh kosong.',
            'nama_matkul.max' => 'Nama Mata Kuliah maksimal 100 karakter.',
            'nama_matkul.regex' => 'Nama Mata Kuliah tidak boleh mengandung simbol',

            'prodi_id.required' => 'Program Studi wajib dipilih',

            'semester.required' => 'Semester wajib dipilih',

            'tahun_ajaran_id.required' => 'Tahun Ajaran harus dipilih.',

            'durasi_matkul.required' => 'Jumlah SKS tidak boleh kosong.',
            'durasi_matkul.integer' => 'Jumlah SKS harus berupa angka.',
            'durasi_matkul.min' => 'Minimal 1 SKS.',
            'durasi_matkul.max' => 'Maksimal 10 SKS.',
        ];
    }
}
