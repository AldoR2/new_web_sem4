<?php

namespace App\Http\Requests\Admin;

use App\Models\Mahasiswa;
use App\Models\Pertemuan;
use App\Models\Presensi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;


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

    public function rules():array
    {

        // $id = $id ??  $this->route('presensi');
        $status = $this->input('status'); // ambil status yang dipilih user

        $rules = [
            'inputs.*.tgl_presensi' => ['required'],
            'prodi_id' => 'required',
            'semester' => ['required', $this->cekMahasiswa()],
            'status' => ['required', $this->ujianRule()],
            'inputs.*.pertemuan_ke' => ['required', $this->pertemuanRule()],
            'matkul_id' => [
                'required',
                Rule::exists('matkuls', 'id')->where(function ($query) {
                    $query->where('prodi_id', $this->input('prodi_id'))
                        ->where('semester', $this->input('semester'));
                }),
            ],
        ];

        if ($status !== 'libur') {
            $rules['ruangan_id'] = ['required', $this->ruanganRule()];
            $rules['inputs.*.jam_awal']   = ['required'];
            $rules['inputs.*.jam_akhir']  = ['required','after:inputs.*.jam_awal', $this->jadwalRule()];
        }

        if ($status === 'aktif') {
            $rules['inputs.*.jenis'] = ['required','in:teori,praktik'];
        }

        if (auth()->user()->role === 'admin') {
            $rules['dosen_id'] = ['required', $this->dosenRule()];
        }

        return $rules;
    }

    public function messages(){
        return [
            'inputs.*.tgl_presensi.required' => 'Pilih tanggal presensi dahulu.',
            'inputs.*.jam_awal.required' => 'Tentukan Jam Mulai Presensi.',

            'inputs.*.jam_akhir.required' => 'Tentukan Jam Selesai Presensi.',
            'inputs.*.jam_akhir.after' => 'Jam Selesai Presensi harus lebih besar',

            'inputs.*.pertemuan_ke.required' => 'Silahkah pilih Pertemuan',

            'status.required' => 'Silahkah pilih Status Pertemuan',

            'dosen_id.required' => 'Silahkah pilih dosen',

            'prodi_id.required' => 'Silahkah pilih Program Studi',

            'semester.required' => 'Silahkah pilih semester',

            'matkul_id.required' => 'Silahkah pilih Mata Kuliah',
            'matkul_id.exists' => 'Mata kuliah tidak valid untuk prodi dan semester yang dipilih.',

            'ruangan_id.required' => 'Silahkah pilih ruangan',

            'inputs.*.jenis.required' => 'Silahkah pilih Jenis Perkuliahan',
        ];
    }

private function pertemuanRule()
{
    return function ($attribute, $value, $fail) {
        $index = explode('.', $attribute)[1] ?? null; // ambil index inputs.*
        if ($index === null) return;

        $pertemuanKe = $this->input("inputs.$index.pertemuan_ke");

        $conflict = Pertemuan::where('prodi_id', $this->input('prodi_id'))
            ->where('semester', $this->input('semester'))
            ->where('matkul_id', $this->input('matkul_id'))
            ->where('pertemuan_ke', $pertemuanKe)
            ->exists();

        if ($conflict) {
            $fail("Pertemuan Ke-$pertemuanKe sudah ada untuk mata kuliah ini (baris ke-".($index+1).").");
        }

        $inputs = $this->input('inputs', []);
        foreach ($inputs as $i => $input) {
            if ($i == $index) continue; // skip diri sendiri

            $pertemuanKe2 = $input['pertemuan_ke'] ?? null;
            if ($pertemuanKe2 && $pertemuanKe2 == $pertemuanKe) {
                $fail("Pertemuan Ke-$pertemuanKe duplikat dengan input ke-" . ($i+1) );
            }
        }
    };
}


private function ruanganRule(){
    return function($attribute, $value, $fail){
        foreach ($this->inputs as $i => $input) {
            $jamAwal = $input['jam_awal'] ?? null;
            $jamAkhir = $input['jam_akhir'] ?? null;
            $tglPresensi = $input['tgl_presensi'] ?? null;

            $conflictRuangan = Presensi::where('tgl_presensi',$tglPresensi)
                ->where('ruangan_id', $this->input('ruangan_id'))
                ->where(function($query) use ($jamAwal, $jamAkhir){
                    $query->where(function ($q) use ($jamAwal) {
                        $q->where('jam_awal', '<=', $jamAwal)
                        ->where('jam_akhir', '>', $jamAwal);
                    })->orWhere(function ($q) use ($jamAkhir) {
                        $q->where('jam_awal', '<', $jamAkhir)
                        ->where('jam_akhir', '>=', $jamAkhir);
                    })->orWhere(function ($q) use ($jamAwal, $jamAkhir) {
                        $q->where('jam_awal', '>=', $jamAwal)
                        ->where('jam_akhir', '<=', $jamAkhir);
                    });
                })->exists();

            if ($conflictRuangan) {
                $fail("Ruangan sedang dipakai pada waktu tersebut (input ke-" . ($i+1) . ").");
            }
        }
    };
}

private function dosenRule(){
    return function($attribute, $value, $fail){
        foreach ($this->inputs as $i => $input) {
            $jamAwal = $input['jam_awal'] ?? null;
            $jamAkhir = $input['jam_akhir'] ?? null;
            $tglPresensi = $input['tgl_presensi'] ?? null;

            $conflictDosen = Presensi::where('tgl_presensi', $tglPresensi)
                ->where('dosen_id', $this->input('dosen_id'))
                ->where(function ($query) use ($jamAwal, $jamAkhir){
                    $query->where(function ($q) use ($jamAwal){
                        $q->where('jam_awal', '<=', $jamAwal)
                        ->where('jam_akhir', '>', value: $jamAwal);
                    })->orWhere(function ($q) use ($jamAkhir) {
                        $q->where('jam_awal', '<', $jamAkhir)
                        ->where('jam_akhir', '>=', $jamAkhir);
                    });
                })->exists();

            if ($conflictDosen) {
                $fail("Dosen bentrok pada waktu tersebut (input ke-" . ($i+1) . ").");
            }
        }
    };
}

private function jadwalRule(){
    return function($attribute, $value, $fail){
        $index = explode('.', $attribute)[1] ?? null;
        if ($index === null) return;

        $jamAwal = $this->input("inputs.$index.jam_awal");
        $jamAkhir = $this->input("inputs.$index.jam_akhir");
        $tglPresensi = $this->input("inputs.$index.tgl_presensi");

        $conflictJadwal = Presensi::where('tgl_presensi', $tglPresensi)
            ->whereHas('pertemuan', function ($query) {
                $query->where('prodi_id', $this->input('prodi_id'))
                    ->where('semester', $this->input('semester'));
            })->where(function ($query) use ($jamAwal, $jamAkhir) {
                $query->where(function ($q) use ($jamAwal) {
                    $q->where('jam_awal', '<=', $jamAwal)
                    ->where('jam_akhir', '>', $jamAwal);
                })->orWhere(function ($q) use ($jamAkhir) {
                    $q->where('jam_awal', '<', $jamAkhir)
                    ->where('jam_akhir', '>=', $jamAkhir);
                })->orWhere(function ($q) use ($jamAwal, $jamAkhir) {
                    $q->where('jam_awal', '>=', $jamAwal)
                    ->where('jam_akhir', '<=', $jamAkhir);
                });
            })->exists();

        if ($conflictJadwal) {
            $fail("Jadwal bentrok untuk prodi & semester.");
        }
        // === 2. Cek bentrok antar input dalam request ===
        $inputs = $this->input('inputs', []);
        foreach ($inputs as $i => $input) {
            if ($i == $index) continue; // jangan cek diri sendiri

            $jamAwal2 = $input['jam_awal'] ?? null;
            $jamAkhir2 = $input['jam_akhir'] ?? null;
            $tgl2 = $input['tgl_presensi'] ?? null;

            if (!$jamAwal2 || !$jamAkhir2 || !$tgl2) continue;

            // hanya cek jika tanggal sama
            if ($tglPresensi === $tgl2) {
                // logika overlap
                if (($jamAwal < $jamAkhir2) && ($jamAkhir > $jamAwal2)) {
                    $fail("Jadwal bentrok dengan input ke-" . ($i+1) . " pada request ini.");
                }
            }
        }
    };
}

private function ujianRule(){
    return function ($attribute, $value, $fail){
        if (in_array($this->input('status'), ['uts', 'uas'])) {
            $conflictUjian = Pertemuan::where('prodi_id', $this->input('prodi_id'))
                ->where('semester', $this->input('semester'))
                ->where('matkul_id', $this->input('matkul_id'))
                ->where('status', $this->input('status'))
                ->exists();

            if ($conflictUjian) {
                $fail('Perkuliahan untuk ' . strtoupper($this->input('status')) . ' sudah ada.');
            }
        }
    };
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


public function withValidator($validator)
{
    $validator->after(function ($validator) {
        if ($this->input('status') === 'aktif') {
            foreach ($this->inputs as $i => $input) {

                $jamAwal = $input['jam_awal'] ?? null;
                $jamAkhir = $input['jam_akhir'] ?? null;

                if ($jamAwal && $jamAkhir) {
                    $awal  = strtotime($jamAwal);
                    $akhir = strtotime($jamAkhir);

                    if ($awal && $akhir) {
                        // Jam akhir harus lebih besar dari jam awal
                        if ($akhir <= $awal) {
                            $validator->errors()->add(
                                "inputs.$i.jam_akhir",
                                "Jam selesai harus lebih besar dari jam mulai pada baris ke-" . ($i + 1)
                            );
                        }

                        // Durasi minimal 30 menit
                        if (($akhir - $awal) < 30 * 60) {
                            $validator->errors()->add(
                                "inputs.$i.jam_awal",
                                "Durasi perkuliahan minimal 30 menit pada baris ke-" . ($i + 1)
                            );
                        }
                    }
                }
            }
        }
    });
}




}
