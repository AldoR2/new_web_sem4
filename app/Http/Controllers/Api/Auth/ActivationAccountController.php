<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\OtpToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ActivationAccountController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        $mahasiswa = Mahasiswa::where('email', $email)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email tidak ditemukan',
            ], 404);
        }

        if ($mahasiswa->email_verified_at) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Akun sudah tervalidasi, silahkan login',
            ], 400);
        }

        // Generate OTP dan simpan token hash
        $otp = random_int(1000, 9999);
        $hashedOtp = bcrypt($otp);
        $expiredAt = Carbon::now()->addMinutes(15);

        // Insert / update ke tabel otp_tokens
        DB::table('otp_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $hashedOtp,
                'expired_at' => $expiredAt,
            ]
        );

        // Kirim email (langsung HTML body)
        try {
            Mail::raw('', function ($message) use ($email, $mahasiswa, $otp) {
                $message->to($email)
                    ->subject('Validasi Akun')
                    ->html("
                    Halo <b>{$mahasiswa->nama}</b>,<br>
                    Berikut NIM dan kode OTP kamu, masukkan kode ini untuk melanjutkan validasi akun kamu.<br><br>
                    <h2>NIM: <b>{$mahasiswa->nim}</b></h2>
                    <h2>OTP: <b>{$otp}</b></h2>
                    Kode OTP ini akan kedaluwarsa dalam 15 menit!<br><br>
                    Demi keamanan, jangan beritahu kode tersebut kepada siapapun dan segera ganti password kamu dengan ketat.
                    ");
            });

            return response()->json([
                'status' => 'success',
                'message' => 'OTP berhasil dikirim',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function checkOtp(Request $request)
    {
        $otpRecord = OtpToken::where('email', $request->email)
            ->where('expired_at', '>=', Carbon::now())
            ->first();

        $mahasiswa = Mahasiswa::where('email', $request->email)->first();

        if (!$otpRecord || !$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP tidak ditemukan'
            ], 404);
        }

        if ($mahasiswa->nim !== $request->nim) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIM tidak sesuai'
            ]);
        }

        if (!Hash::check($request->otp, $otpRecord->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP tidak valid atau telah kedaluwarsa'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP is valid'
        ]);
    }

    public function validateAccount(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|exists:mahasiswas,nim',
            'password' => 'required|string|min:6',
        ]);

        $nim = $request->nim;
        $password = Hash::make($request->password);
        $emailVerifiedAt = Carbon::now('Asia/Jakarta')->toDateTimeString();

        $updated = DB::table('mahasiswas')
            ->join('users', 'mahasiswas.user_id', '=', 'users.id')
            ->where('mahasiswas.nim', $nim)
            ->update([
                'users.password' => $password,
                'mahasiswas.email_verified_at' => $emailVerifiedAt,
            ]);

        if ($updated > 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Validation Success',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No rows updated',
        ], 400);
    }
}
