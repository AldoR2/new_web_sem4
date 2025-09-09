<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreMasterKalender;
use App\Models\KalenderAkademik;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KalenderAkademikController extends Controller
{
    public function index()
    {
        $title = 'Data Kalender Akademik';
        $kalenders = KalenderAkademik::latest()->get();

        return view('admin.master_data.kalender', compact('kalenders', 'title'));
    }
    public function create()
    {
        $title = 'Tambah Data';
        $subtitle = 'Silahkan Tambahkan Data Kalender Akademik';
        return view('admin.master_data.form-kalender', compact('title','subtitle'));
    }

    public function store(StoreMasterKalender $request)
    {

        try {
            DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data = [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status,
            ];

            KalenderAkademik::create($data);
        });

            return redirect()->route('admin.kalender-akademik.index')->with([
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan kalender akademik', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menambahkan data: ' . $e->getMessage()
            ]);
        }
    }

    public function edit(string $id)
    {
        $title = 'Edit Data';
        $subtitle = 'Silahkan Perbarui Data Kalender Akademik';
        $kalender = KalenderAkademik::findOrFail($id);
        return view('admin.master_data.form-kalender', compact('kalender', 'title','subtitle'));
    }

    public function update(StoreMasterKalender $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $kalender = KalenderAkademik::findOrFail($id);
                $data = $request->validated();
                $data = [
                    'judul' => $request->judul,
                    'deskripsi' => $request->deskripsi,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'status' => $request->status,
                ];

                $kalender->update($data);
            });

            return redirect()->route('admin.kalender-akademik.index')->with([
                'status' => 'success',
                'message' => 'Data berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui kalender akademik', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $kalender = KalenderAkademik::findOrFail($id);
            $kalender->delete();

            return redirect()->route('admin.kalender-akademik.index')->with([
                'status' => 'success',
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus kalender akademik', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.kalender-akademik.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ]);
        }
    }
    public function viewCalendar()
    {
        $title = 'Lihat Kalender Akademik';

        $kalenders = KalenderAkademik::all();

        $events = $kalenders->map(function($item) {
            return [
                'title' => $item->judul,
                'start' => $item->tanggal_mulai,
                'end' => $item->tanggal_selesai ? Carbon::parse($item->tanggal_selesai)->addDay()->toDateString() : null,
                'description' => $item->deskripsi,
                'color' => $item->status == 0 ? '#ef4444' : '#2563eb',
            ];
        })->values();

        return view('view-kalender', compact('title', 'events'));
    }

}
