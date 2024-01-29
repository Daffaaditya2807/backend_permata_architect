<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\Proyeks;
use App\Models\Progress;
use App\Models\Pengeluarans;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ProyekController extends Controller
{
    //
    public function all(Request $request)
    {
        $id_proyek = $request->input('id_proyek');
        $lokasi_proyek = $request->input('lokasi_proyek');
        $limit = $request->input('limit', 6);

        if ($id_proyek) {
            $proyeks = Proyeks::with(['pengeluarans', 'pekerjas'])->find($id_proyek);
            if ($proyeks) {
                return ResponseFormatter::success($proyeks, 'Data Produk berhasil diambil');
            } else {
                return ResponseFormatter::error(null, 'Data Produk tidak ada', 404);
            }
        }

        $proyeks =  Proyeks::with(['pengeluarans', 'pekerjas']);

        if ($lokasi_proyek) {
            $proyeks->where('lokasi_proyek', 'like', '%' . $lokasi_proyek . '%');
        }

        return ResponseFormatter::success($proyeks->paginate($limit), 'Data Produk berhasil diambil');
    }

    public function totalPengeluaranProyek()
    {
        //Jika hanya Menampilkan projects yan
        $projects = Proyeks::all()->map(function ($project) {
            $totalPengeluaran = $project->getTotalPengeluaranAttribute();
            if ($totalPengeluaran > 0) {
                $project->total_pengeluaran = $totalPengeluaran;
                unset($project->pengeluarans); // Remove the pengeluarans relationship
                return $project;
            }
        })->filter();


        if ($projects) {
            return ResponseFormatter::success($projects, 'Berhasil Ditambah');
        } else {
            return ResponseFormatter::error(null, 'Gagal', 404);
        }
    }

    public function addprogress(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'detail_lokasi' => ['required', 'string', 'max:500'],
                'progress' => ['required', 'string', 'max:255'],
                'keterangan_progress' => ['required', 'string'],
                'image' => ['required', 'image', 'max:2048'],
                'id_proyek' => ['required', 'int', 'max:255'],
            ]);

            // Menangani upload gambar
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $timestamp = Carbon::now()->format('YmdHis'); // Menggunakan Carbon untuk timestamp
                $filename = 'Progress_' . $timestamp . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/images/progress_image', $filename);
                $picUrl = Storage::url($path);
            } else {
                $picUrl = null; // Atau handle jika tidak ada gambar yang diupload
            }

            $progress =  Progress::create([
                'name' => $request->name,
                'detail_lokasi' => $request->detail_lokasi,
                'progress' => $request->progress,
                'tanggal' => Carbon::now(),
                'keterangan_progress' => $request->keterangan_progress,
                'picUrl' => $picUrl,
                'id_proyek' => $request->id_proyek
            ]);
            return ResponseFormatter::success($progress, 'Berhasil ditambahkan');
        } catch (Exception $error) {
            return ResponseFormatter::success($error->getMessage(), 'Gagal ditambahkan');
        }
    }

    public function getProgress(Request $request)
    {
        $id_progress = $request->input('id_proyek');
        $limit = $request->input('limit', 6);
        $progress =  Progress::query();
        if ($id_progress) {
            $progress->where('id_proyek', '=', '' . $id_progress . '');
        }
        return ResponseFormatter::success($progress->paginate($limit), 'Data Progress berhasil diambil');
    }
}
