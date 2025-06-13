<?php

namespace App\Imports;

use App\Models\Admin;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\Provinsi;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


class AdminImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
            $provinsi = Provinsi::where('name', $row['provinsi'])->first();
            $kota = Kota::where('name', $row['kota'])->first();
            $kecamatan = Kecamatan::where('name', $row['kecamatan'])->first();
            $kelurahan = Kelurahan::where('name', $row['kelurahan'])->first();

            $user = User::create([
                'name' => ucwords($row['nama']),
                'email' => trim($row['email']),
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]);

            return new Admin([
                'user_id' => $user->id,
                'nama' => trim(ucwords($row['nama'])),
                'email' => trim($row['email']),
                'jenis_kelamin' => trim(strtoupper($row['jenis_kelamin'])),
                'agama' => trim(ucwords($row['agama'])),
                'tempat_lahir' => trim(ucwords($row['tempat_lahir'])),
                'tgl_lahir' => trim(is_numeric($row['tgl_lahir']) ? ExcelDate::excelToDateTimeObject($row['tgl_lahir'])->format('Y-m-d') : date('Y-m-d', strtotime($row['tgl_lahir']))),
                'no_telp' => trim($row['no_telp']),
                'alamat' => trim($row['alamat']),
                'provinsi_id' => trim(strtoupper($provinsi?->id)),
                'kota_id' => trim(strtoupper($kota?->id)),
                'kecamatan_id' => trim(strtoupper($kecamatan?->id)),
                'kelurahan_id' => trim(strtoupper($kelurahan?->id)),
            ]);
    }
      public function rules(): array
    {
        return [
            '*.nama' => 'required|max:100',
            '*.email' => 'required|email|max:100|unique:users,email',
            '*.jenis_kelamin' => 'required',
            '*.agama' => 'required',
            '*.tempat_lahir' => 'required|max:100',
            '*.tgl_lahir' => 'required',
            '*.no_telp' => 'required|regex:/^[0-9]+$/|max:20',
            '*.alamat' => 'required|max:200',
            '*.provinsi' => 'required|exists:provinsis,name',
            '*.kota' => 'required|exists:kotas,name',
            '*.kecamatan' => 'required|exists:kecamatans,name',
            '*.kelurahan' => 'required|exists:kelurahans,name',
        ];
    }


    public function onFailure(...$failures)
{
    foreach ($failures as $failure) {
        $row = $failure->row(); // baris (1-based, termasuk heading)
        $attribute = $failure->attribute(); // kolom yang gagal
        $errors = $failure->errors(); // array pesan error

        Log::warning("Gagal import baris {$row}, kolom '{$attribute}': ".implode('; ', $errors));
    }
}


// public function onFailure(Failure ...$failures)
// {
//     foreach ($failures as $failure) {
//         $row = $failure->row();
//         $attributes = is_array($failure->attribute()) ? implode(', ', $failure->attribute()) : $failure->attribute();
//         $errors = is_array($failure->errors()) ? implode(', ', $failure->errors()) : $failure->errors();

//         \Log::error("Import gagal di baris $row untuk kolom $attributes: $errors");
//     }
// }



    //     public function onFailure(Failure ...$failures)
    // {
    //     // Bisa log, tampilkan flash message, atau abaikan
    //     foreach ($failures as $failure) {
    //         // contoh: log errornya
    //         \Log::error('Import gagal di baris '.$failure->row().' untuk kolom '.implode(', ', $failure->attribute()).': '.implode(', ', $failure->errors()));
    //     }
    // }
}
