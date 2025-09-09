<?php

namespace App\Http\Requests\Admin;

use App\Models\Pertemuan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;


class UpdatePresensi extends FormRequest
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

    public function rules($id = null):array
    {

        $id = $this->route('presensi');
        $presensi = Presensi::with('pertemuan')->findOrFail($id);
        $status = $this->input('status'); // ambil status yang dipilih user

        $rules = [
            'pertemuan_ke' => ['required', $this->pertemuanRule($presensi)],
            'status' => ['required', $this->ujianRule($presensi)],
            'tgl_presensi' => ['required', $this->cekJam($presensi)],
            'prodi_id' => 'required',
            'semester' => 'required',
            'matkul_id' => [
                'required',
                Rule::exists('matkuls', 'id')->where(function ($query) {
                    $query->where('prodi_id', $this->input('prodi_id'))
                        ->where('semester', $this->input('semester'));
                }),
            ],
        ];

        if ($status !== 'libur') {
            $rules['ruangan_id']   = ['required', $this->ruanganRule($presensi)];
            $rules['jam_awal']     = ['required'];
            $rules['jam_akhir']    = ['required','after:jam_awal.*', $this->jadwalRule($presensi)];
        }
        if ($status === 'aktif') {
            $rules['jenis']   = ['required','in:teori,praktik'];
        }

        if (auth()->user()->role === 'admin') {
            $rules['dosen_id'] = ['required', $this->dosenRule($presensi)];
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
            'matkul_id.exists' => 'Mata kuliah tidak valid untuk prodi dan semester yang dipilih.',

            'ruangan_id.required' => 'Silahkah pilih ruangan',

            'jenis.required' => 'Silahkah pilih Jenis Perkuliahan',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('status') === 'aktif'){
                $jam_awal = strtotime($this->input('jam_awal'));
                $jam_akhir = strtotime($this->input('jam_akhir'));

                if ($jam_awal && $jam_akhir) {
                    $durasi = $jam_akhir - $jam_awal;
                    if ($durasi < 30 * 60) {
                        $validator->errors()->add('jam_awal', 'Durasi Perkuliahan harus minimal 30 menit.');
                    }
                }
            }
        });
    }

    private function cekJam($presensi){
        return function ($attribute, $value, $fail) use ($presensi){
            if ($presensi->jam_awal) {

                $now = now();
                $presensiStart = Carbon::parse($presensi->tgl_presensi . ' ' . $presensi->jam_awal);

                if ($now->gte($presensiStart)) {
                        $fail('Perkuliahan tidak dapat diedit karena perkuliahan sedang berlangsung atau sudah selesai.');
                }
            }
        };
    }

    private function pertemuanRule($presensi){
        return function ($attribute, $value, $fail) use ($presensi) {

            $pertemuanKe = $this->input("pertemuan_ke");

            $conflict = Pertemuan::where('prodi_id', $this->input('prodi_id'))
                ->where('semester', $this->input('semester'))
                ->where('matkul_id', $this->input('matkul_id'))
                ->where('pertemuan_ke', $pertemuanKe)
                ->where('id', '!=', $presensi->pertemuan->id)
                ->exists();

            if ($conflict) {
                $fail(" Perkuliahan untuk Pertemuan Ke-$pertemuanKe sudah ada.");
            }
        };
    }

    private function ruanganRule($presensi){
        return function($attribute, $value, $fail) use ($presensi){

                $jamAwal = $this->input('jam_awal');
                $jamAkhir = $this->input('jam_akhir');
                $tglPresensi = $this->input('tgl_presensi');

                $conflictRuangan = Presensi::where('tgl_presensi',$tglPresensi)
                    ->where('ruangan_id', $this->input('ruangan_id'))
                    ->where('id', '!=', $presensi->id)
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
                    $fail("Ruangan sedang dipakai pada waktu tersebut");
                }
        };
    }

    private function dosenRule($presensi){
        return function($attribute, $value, $fail) use ($presensi){
                $jamAwal = $this->input('jam_awal');
                $jamAkhir = $this->input('jam_akhir');
                $tglPresensi = $this->input('tgl_presensi');

                $conflictDosen = Presensi::where('tgl_presensi', $tglPresensi)
                    ->where('dosen_id', $this->input('dosen_id'))
                    ->where('id', '!=', $presensi->id)
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
                    $fail("Dosen bentrok pada jadwal Perkuliahan tersebut");
                }
        };
    }

    private function jadwalRule($presensi){
        return function($attribute, $value, $fail) use ($presensi){

            $jamAwal = $this->input("jam_awal");
            $jamAkhir = $this->input("jam_akhir");
            $tglPresensi = $this->input("tgl_presensi");

            $conflictJadwal = Presensi::where('tgl_presensi', $tglPresensi)
                ->where('id', '!=', $presensi->id)
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
                $fail("Jadwal bentrok untuk program studi & semester tersebut.");
            }
        };
    }

    private function ujianRule($presensi){
        return function ($attribute, $value, $fail) use ($presensi){
            if (in_array($this->input('status'), ['uts', 'uas'])) {
                $conflictUjian = Pertemuan::where('prodi_id', $this->input('prodi_id'))
                    ->where('semester', $this->input('semester'))
                    ->where('matkul_id', $this->input('matkul_id'))
                    ->where('status', $this->input('status'))
                    ->where('id', '!=', $presensi->pertemuan->id)
                    ->exists();

                if ($conflictUjian) {
                    $fail('Perkuliahan untuk ' . strtoupper($this->input('status')) . ' sudah ada.');
                }
            }
        };
    }

//     public function withValidator($validator)
// {
//     $validator->after(function ($validator) {
//         if ($this->input('status') === 'aktif') {
//             $jamAwals = $this->input('jam_awal', []);
//             $jamAkhirs = $this->input('jam_akhir', []);

//             foreach ($jamAwals as $i => $jamAwal) {
//                 $jamAkhir = $jamAkhirs[$i] ?? null;

//                 if ($jamAwal && $jamAkhir) {
//                     $awal  = strtotime($jamAwal);
//                     $akhir = strtotime($jamAkhir);

//                     if ($awal && $akhir) {
//                         $durasi = $akhir - $awal;
//                         if ($durasi < 30 * 60) {
//                             $validator->errors()->add(
//                                 "jam_awal.$i",
//                                 "Durasi perkuliahan minimal 30 menit pada baris ke-" . ($i + 1)
//                             );
//                         }
//                     }
//                 }
//             }
//         }
//     });
// }




    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         if ($this->input('status') === 'aktif'){
    //             $jam_awal = strtotime($this->input('jam_awal'));
    //             $jam_akhir = strtotime($this->input('jam_akhir'));

    //             if ($jam_awal && $jam_akhir) {
    //                 $durasi = $jam_akhir - $jam_awal;
    //                 if ($durasi < 30 * 60) {
    //                     $validator->errors()->add('jam_awal', 'Durasi Perkuliahan harus minimal 30 menit.');
    //                 }
    //             }
    //         }
    //     });
    // }



}
