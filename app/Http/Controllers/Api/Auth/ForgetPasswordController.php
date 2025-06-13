<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\OtpToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Exception;

class ForgetPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email tidak valid'
            ], 422);
        }

        $email = $request->email;

        $user = Mahasiswa::where('email', $email)->first();
        $role = 'mahasiswa';

        if (!$user) {
            $user = Dosen::where('email', $email)->first();
            $role = 'dosen';
        }

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email tidak ditemukan'
            ], 404);
        }

        if ($role === 'mahasiswa' && $user->email_verified_at === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lakukan verifikasi email terlebih dahulu'
            ], 403);
        }

        $otp = rand(1000, 9999);
        $hashedOtp = Hash::make($otp);
        $expiredAt = Carbon::now()->addMinutes(15);

        // Simpan token
        OtpToken::updateOrCreate(
            ['email' => $email],
            ['token' => $hashedOtp, 'expired_at' => $expiredAt]
        );

        try {
            Mail::send([], [], function ($message) use ($email, $otp, $user) {
                $message->to($email)
                    ->subject('Reset Password')
                    ->html('
                    Halo <b>' . $user->nama . ',</b><br>
                    Kode OTP Anda adalah: <b>' . $otp . '</b><br>
                    Kode ini akan kedaluwarsa dalam 15 menit.<br>
                    Jika Anda tidak meminta reset, abaikan email ini.
                ');
                $message->from('stikes.pantiwaluya@gmail.com', 'STIPRES');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'OTP berhasil dikirim'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage()
            ], 500);
        }
    }
    public function checkOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email dan OTP wajib diisi'
            ], 422);
        }

        $email = $request->email;
        $otp = $request->otp;

        $otpToken = OtpToken::where('email', $email)
            ->where('expired_at', '>=', Carbon::now())
            ->first();

        if (!$otpToken || !Hash::check($otp, $otpToken->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP tidak valid atau telah kedaluwarsa'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP valid'
        ], 200);
    }

}
