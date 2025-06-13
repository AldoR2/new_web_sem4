<?php

namespace App\Http\Requests\Admin;

use App\Models\TahunAjaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreMasterTahun extends FormRequest
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
            'tahun_awal' => 'required|max:4|regex:/^[0-9]+$/',
            'tahun_akhir' => 'required|max:4|regex:/^[0-9]+$/',
            'keterangan' => 'required',
        ];
    }

    public function messages(){
        return [
            'tahun_awal.required' => 'Tahun Awal tidak boleh kosong.',
            'tahun_awal.max' => 'Tahun Awal maksimal 4 angka.',
            'tahun_awal.regex' => 'Tahun Awal hanya boleh berupa angka.',

            'tahun_akhir.required' => 'Tahun Akhir tidak boleh kosong.',
            'tahun_akhir.max' => 'Tahun Akhir maksimal 4 angka.',
            'tahun_akhir.regex' => 'Tahun Akhir hanya boleh berupa angka.',

            'keterangan.required' => 'Pilih Keterangan terlebih dahulu',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $exists = TahunAjaran::where('tahun_awal', $this->tahun_awal)
                ->where('tahun_akhir', $this->tahun_akhir)
                ->where('keterangan', $this->keterangan)
                ->when($this->isMethod('put'), function ($query) {
                    $query->where('id', '!=', $this->route('master_tahun')); // nama parameter di route
                })
                ->exists();

            if ($exists) {
                $validator->errors()->add('tahun_awal', 'Kombinasi Tahun ajaran sudah ada.');
            }

            if (is_numeric($this->tahun_awal) && is_numeric($this->tahun_akhir)) {
                if ((int)$this->tahun_akhir !== (int)$this->tahun_awal + 1) {
                    $validator->errors()->add('tahun_akhir', 'Tahun Akhir harus satu tahun setelah Tahun Awal.');
                }
            }
        });
    }
}
