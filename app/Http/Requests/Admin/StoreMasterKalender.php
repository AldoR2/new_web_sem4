<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreMasterKalender extends FormRequest
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

        $id = $id ?? $this->route('kalender_akademik');

        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required'
        ];
    }

    public function messages(){
        return [
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'tanggal_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'status.required' => 'Silahkan Pilih Status.',
        ];
    }

    public function prepareForValidation(){
        $data = [
            'judul' => trim($this->judul),
            'deskripsi' => trim($this->deskripsi),
        ];

        $this->merge($data);
    }
}
