<?php

namespace App\Http\Requests\Admin;

use App\Models\Mahasiswa;
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
            'semester' => ['required', $this->cekMahasiswa()],
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

    private function cekMahasiswa(){
        return function($attribute, $value, $fail){
            $mahasiswa = Mahasiswa::where('prodi_id', $this->input('prodi_id'))
            ->where('semester', $this->input('semester'))->exists();

            if(!$mahasiswa){
                $fail('Tidak ada Mahasiswa pada Program Studi dan Semester tersebut');
            }
        };
    }

}
