<?php

namespace App\Http\Controllers\Api\activity;

use App\Http\Controllers\Controller;
use App\Models\KalenderAkademik;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index(Request $request)
    {
        $calendar = KalenderAkademik::select('id','judul', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai', 'status')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data kalender akademik berhasil ditampilkan',
            'data' => $calendar
        ]);
    }
}
