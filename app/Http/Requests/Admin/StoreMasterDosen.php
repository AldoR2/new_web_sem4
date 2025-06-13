<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreMasterDosen extends FormRequest
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

        $id = $id ??  $this->route('master_dosen');

        return [
            'nip' => ['required', 'max:20','regex:/^[0-9]+$/', Rule::unique('dosens', 'nip')->ignore($id),],
            'nama' => 'required|max:100|regex:/^[A-Za-z\s]+$/',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'tempat_lahir' => 'required|max:100|regex:/^[A-Za-z\s]+$/',
            'tgl_lahir' => 'required|before:today',
            'no_telp' => 'required|max:20|regex:/^[0-9]+$/',
            'email' => ['required','email','max:100',Rule::unique('dosens', 'email')->ignore($id),],
            'alamat' => 'required|max:200',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // opsional: validasi foto
            'provinsi_id' => 'required',
            'kota_id' => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
        ];
    }

    public function messages(){
        return [
            'nip.required' => 'NIP tidak boleh kosong',
            'nip.max' => 'NIP tidak boleh melebihi 18 Karakter',
            'nip.regex' =>'NIP hanya boleh mengandung angka',
            'nip.unique' => 'NIP sudah terdaftar',

            'nama.required' => 'Nama tidak boleh kosong',
            'nama.max' => 'Nama maksimal 100 karakter',
            'nama.regex' =>'Nama hanya boleh mengandung huruf',

            'jenis_kelamin.required' => 'Jenis Kelamin harus dipilih',
            'agama.required' => 'Agama harus dipilih',

            'tempat_lahir.required' => 'Tempat Lahir tidak boleh kosong',
            'tempat_lahir.max' => 'Tempat Lahir tidak boleh melebihi 100 karakter',
            'tempat_lahir.regex' =>'Nama hanya boleh mengandung huruf',

            'tgl_lahir.required' => 'Tanggal Lahir wajib diisi',
            'tgl_lahir.before' => 'Tanggal Lahir harus sebelum hari ini',

            'no_telp.required' => 'Nomor Telepon wajib diisi',
            'no_telp.max' => 'Nomor Telepon maksimal 20 karakter',
            'no_telp.regex' => 'Nomor Telepon hanya boleh berisi angka',

            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 100 karakter',
            'email.unique' => 'Email sudah digunakan',

            'alamat.required' => 'Alamat tidak boleh kosong',
            'alamat.max' => 'Alamat maksimal 200 karakter',

            'prodi_id.required' => 'Program Studi wajib dipilih',

            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran gambar maksimal 2MB',

            'provinsi_id.required' => 'Provinsi wajib dipilih',
            'kota_id.required' => 'Kota wajib dipilih',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih',
        ];
    }
}
