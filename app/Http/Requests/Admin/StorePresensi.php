<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StorePresensi extends FormRequest
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

        $id = $id ??  $this->route('presensi');

        $rules = [
            'tgl_presensi' => 'required',
            'jam_awal' => 'required',
            'jam_akhir' => 'required|after:jam_awal',
            'prodi_id' => 'required',
            'semester' => 'required',
            'matkul_id' => 'required',
            'ruangan_id' => 'required',
        ];

        // Hanya validasi dosen_id jika role-nya admin
        if (auth()->user()->role === 'admin') {
            $rules['dosen_id'] = 'required';
        }

        return $rules;
    }

    public function messages(){
        return [
            'tgl_presensi.required' => 'Pilih tanggal presensi dahulu.',
            'jam_awal.required' => 'Tentukan Jam Mulai Presensi.',

            'jam_akhir.required' => 'Tentukan Jam Selesai Presensi.',
            'jam_akhir.after' => 'Jam Selesai Presensi harus lebih besar',

            'dosen_id.required' => 'Silahkah pilih dosen',

            'prodi_id.required' => 'Silahkah pilih Program Studi',

            'semester.required' => 'Silahkah pilih semester',

            'matkul_id.required' => 'Silahkah pilih Mata Kuliah',

            'ruangan_id.required' => 'Silahkah pilih ruangan',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $jam_awal = strtotime($this->input('jam_awal'));
            $jam_akhir = strtotime($this->input('jam_akhir'));

            $durasi = $jam_akhir - $jam_awal;
            if ($durasi < 30 * 60) {
                $validator->errors()->add('jam_awal', 'Durasi Perkuliahan harus minimal 30 menit.');
            }
        });
    }

}
