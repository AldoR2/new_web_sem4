<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreMasterJadwal extends FormRequest
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
        $id = $id ??  $this->route('master_jadwal');

        return [
            'hari' => 'required',
            'jam' => 'required',
            'durasi' => 'required|integer|min:1|max:10',
            'dosen_id' => 'required',
            'prodi_id' => 'required',
            'semester' => 'required',
            'matkul_id' => 'required',
            'ruangan_id' => 'required',
        ];
    }

    public function messages(){
        return [
            'hari.required' => 'Pilih Hari Terlebih Dahulu.',
            'jam.required' => 'Tentukan Jam Awal Presensi.',

            'durasi.required' => 'Jumlah SKS tidak boleh kosong.',
            'durasi.integer' => 'Jumlah SKS harus berupa angka.',
            'durasi.min' => 'Minimal 1 SKS.',
            'durasi.max' => 'Maksimal 10 SKS.',

            'dosen_id.required' => 'Dosen wajib dipilih',

            'prodi_id.required' => 'Program Studi wajib dipilih',

            'semester.required' => 'Semester wajib dipilih',

            'matkul_id.required' => 'Mata Kuliah harus dipilih.',

            'ruangan_id.required' => 'Ruangan harus dipilih.',
        ];
    }

}
