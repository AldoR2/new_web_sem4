<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\FcmToken;
use App\Models\Mahasiswa;
use App\Models\Notification;
use App\Models\User;
use App\Services\FcmV1Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'new_password' => 'required|min:8'
        ]);

        $newPassword = Hash::make($request->new_password);
        $email = $request->email;

        DB::beginTransaction();
        try {
            // Cek mahasiswa berdasarkan email/nim
            $mahasiswa = DB::table('users')
                ->join('mahasiswas', 'mahasiswas.user_id', '=', 'users.id')
                ->where(function ($q) use ($email) {
                    if ($email)
                        $q->orWhere('mahasiswas.email', $email);
                })
                ->select('users.id')
                ->first();

            if ($mahasiswa) {
                $fcmService = new FcmV1Service();

                // Kirim notifikasi ke mahasiswa
                $mahasiswaModel = Mahasiswa::where('email', $email)->first();

                if ($mahasiswaModel) {
                    $Tokens = FcmToken::where('user_id', $mahasiswaModel->user_id)->pluck('token');

                    foreach ($Tokens as $token) {
                        $fcmService->send(
                            $token,
                            'Password Berhasil Diubah',
                            'Password akun Anda telah berhasil diperbarui'
                        );
                    }
                }

                // Ambil data mahasiswa & user
                $user = User::findOrFail($mahasiswa->id); // ini user ID dari hasil join awal
                $mahasiswaModel = Mahasiswa::where('user_id', $user->id)->first(); // untuk nama_user

                $waktu = Carbon::now()->locale('id')->timezone('Asia/Jakarta');
                $tanggal = $waktu->translatedFormat('d F Y');
                $jam = $waktu->format('H.i');
                // Simpan notifikasi
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Password Berhasil Diubah!',
                    'message' => 'Password akun Anda telah berhasil diperbarui.',
                    'type' => 'pengumuman',
                    'nama_user' => $mahasiswaModel->nama,
                    'tanggal' => $tanggal,
                    'jam' => $jam,
                ]);
                DB::table('users')->where('id', $mahasiswa->id)->update([
                    'password' => $newPassword
                ]);
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password berhasil diperbarui untuk mahasiswa.'
                ]);
            }

            // Cek dosen berdasarkan email/nip
            $dosen = DB::table('users')
                ->join('dosens', 'dosens.user_id', '=', 'users.id')
                ->where(function ($q) use ($email) {
                    if ($email)
                        $q->orWhere('users.email', $email);
                })
                ->select('users.id')
                ->first();

            if ($dosen) {
                $fcmService = new FcmV1Service();

                // Kirim notifikasi ke mahasiswa
                $dosenModel = Dosen::where('email', $email)->first();

                if ($dosenModel) {
                    $Tokens = FcmToken::where('user_id', $dosenModel->user_id)->pluck('token');

                    foreach ($Tokens as $token) {
                        $fcmService->send(
                            $token,
                            'Password Berhasil Diubah',
                            'Password akun Anda telah berhasil diperbarui'
                        );
                    }
                }
                // Ambil data dosen & user
                $user = User::findOrFail($dosen->id); // ini user ID dari hasil join awal
                $dosenModel = Dosen::where('user_id', $user->id)->first(); // untuk nama_user

                $waktu = Carbon::now()->locale('id')->timezone('Asia/Jakarta');
                $tanggal = $waktu->translatedFormat('d F Y');
                $jam = $waktu->format('H.i');
                // Simpan notifikasi
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Password Berhasil Diubah!',
                    'message' => 'Password akun Anda telah berhasil diperbarui.',
                    'type' => 'pengumuman',
                    'nama_user' => $dosenModel->nama,
                    'tanggal' => $tanggal,
                    'jam' => $jam,
                ]);

                DB::table('users')->where('id', $dosen->id)->update([
                    'password' => $newPassword
                ]);
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password berhasil diperbarui untuk dosen.'
                ]);
            }

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui password. Pastikan email/NIM/NIP benar.'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
