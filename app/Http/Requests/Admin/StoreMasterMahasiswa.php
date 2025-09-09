<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreMasterMahasiswa extends FormRequest
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

        $id = $id ?? $this->route('master_mahasiswa');

        $rules = [
            'nim' => ['required', 'max:10','min:8', Rule::unique('mahasiswas', 'nim')->ignore($id),],
            'nama' => 'required|max:100|regex:/^[A-Za-z\s]+$/',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'tempat_lahir' => 'required|max:100|regex:/^[A-Za-z\s]+$/',
            'tgl_lahir' => 'required|before:today',
            'no_telp' => 'required|max:20|regex:/^[0-9]+$/',
            'email' => ['required','email','max:100',Rule::unique('mahasiswas', 'email')->ignore($id),],
            'alamat' => 'required|max:200',
            'prodi_id' => 'required',
            'tahun_masuk' => 'required|max:4|regex:/^[0-9]+$/',
            'semester' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // opsional: validasi foto
            'provinsi_id' => 'required',
            'kota_id' => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'rfid' => ['nullable',Rule::unique('mahasiswas','rfid')->ignore($id),]
        ];

            if ($this->isMethod('put') || $this->isMethod('patch')) {
                $rules['new_password'] = 'nullable|min:8';
            }

        return $rules;
    }

    public function messages(){
        return [
            'nim.required' => 'Nim tidak boleh kosong',
            'nim.max' => 'Nim Maksimal 10 Karakter',
            'nim.min' => 'Nim Minimal 8 Karakter',
            'nim.unique' => 'Nim sudah terdaftar',

            'nama.required' => 'Nama tidak boleh kosong',
            'nama.max' => 'Nama tidak boleh melebihi 100 karakter',
            'nama.regex' =>'Nama hanya boleh huruf',

            'jenis_kelamin.required' => 'Jenis Kelamin harus dipilih',
            'agama.required' => 'Agama harus dipilih',

            'tempat_lahir.required' => 'Tempat Lahir tidak boleh kosong',
            'tempat_lahir.max' => 'Tempat Lahir maksimal 100 karakter',
            'tempat_lahir.regex' =>'Tempat Lahir hanya boleh huruf',

            'tgl_lahir.required' => 'Tanggal Lahir wajib diisi',
            'tgl_lahir.before' => 'Tanggal Lahir harus sebelum hari ini',

            'no_telp.required' => 'Nomor Telepon wajib diisi',
            'no_telp.max' => 'Nomor Telepon tidak boleh melebihi 20 karakter',
            'no_telp.regex' => 'Nomor Telepon hanya boleh berisi angka',

            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email tidak boleh melebihi 100 karakter',
            'email.unique' => 'Email sudah digunakan',

            'alamat.required' => 'Alamat tidak boleh kosong',
            'alamat.max' => 'Alamat tidak boleh melebihi 200 karakter',

            'prodi_id.required' => 'Program Studi wajib dipilih',

            'semester.required' => 'Semester wajib dipilih',

            'tahun_masuk.required' => 'Tahun Masuk tidak boleh kosong.',
            'tahun_masuk.max' => 'Tahun Masuk maksimal 4 angka.',
            'tahun_masuk.regex' => 'Tahun Masuk hanya boleh berupa angka.',

            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran gambar maksimal 2MB',

            'provinsi_id.required' => 'Provinsi wajib dipilih',
            'kota_id.required' => 'Kota wajib dipilih',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih',

            'rfid.unique' => 'RFID sudah terdaftar pada mahasiswa lain',

            'new_password.min' => 'Password baru harus minimal 8 karakter'
        ];
    }

    public function prepareForValidation(){
        $data = [
            'nim' => strtoupper(trim($this->nim)),
            'rfid' => trim($this->rfid),
            'nama' => ucwords(trim($this->nama)),
            'tempat_lahir' => ucwords(trim($this->tempat_lahir)),
            'email' => strtolower(trim($this->email)),
            'no_telp' => trim($this->no_telp),
            'alamat' => ucwords(trim($this->alamat)),
            'tahun_masuk' => trim($this->tahun_masuk),
        ];

        if ($this->has('new_password')) {
            $data['password'] = trim($this->new_password);
        }

        $this->merge($data);
    }
}
