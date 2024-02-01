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
use App\Models\Absensis;
use App\Models\Pekerjas;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ProyekController extends Controller
{
    //
    public function all(Request $request)
    {
        $query = $request->input('q');
        $id_proyek = $request->input('id_proyek');

        if ($id_proyek) {
            $proyeks = Proyeks::with(['pengeluarans', 'pekerjas'])->find($id_proyek);
            if ($proyeks) {
                return ResponseFormatter::success($proyeks, 'Data Produk berhasil diambil');
            } else {
                return ResponseFormatter::error(null, 'Data Produk tidak ada', 404);
            }
        }

        $proyeks = Proyeks::with(['pengeluarans', 'pekerjas']);

        if ($query) {
            $proyeks = $proyeks->where(function ($q) use ($query) {
                $q->where('lokasi_proyek', 'like', '%' . $query . '%')
                    ->orWhere('keterangan', 'like', '%' . $query . '%');
            });
        }

        return ResponseFormatter::success($proyeks->get(), 'Data Produk berhasil diambil');
    }

    public function totalPengeluaranProyek()
    {
        //Jika hanya Menampilkan projects yan
        $projects = Proyeks::all()->map(function ($project) {

            $totalPengeluaran = $project->getTotalPengeluaranAttribute();
            if ($totalPengeluaran > 0) {
                $project->total_pengeluaran = $totalPengeluaran;
                unset($project->pengeluarans);
                return $project;
            }
        })->filter();

        $sortedProjects = $projects->sortByDesc('total_pengeluaran')->values()->all();
        if ($sortedProjects) {
            return ResponseFormatter::success($sortedProjects, 'Berhasil Ditambah');
        } else {
            return ResponseFormatter::error(null, 'Gagal', 404);
        }
    }

    public function addprogress(Request $request)
    {
        try {

            //
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
                $timestamp = Carbon::now()->format('YmdHis');
                $filename = 'Progress_' . $timestamp . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/images/progress_image', $filename);
                $picUrl = Storage::url($path);
            } else {
                $picUrl = null;
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
            return ResponseFormatter::error($error->getMessage(), 'Gagal ditambahkan');
        }
    }

    public function getProgress(Request $request)
    {
        try {
            $id_progress = $request->input('id_proyek');
            $progress =  Progress::query();
            if ($id_progress) {
                $progress->where('id_proyek', '=', '' . $id_progress . '');
            }
            $progress->orderBy('tanggal', 'desc');
            return ResponseFormatter::success($progress->get(), 'Data Progress berhasil diambil');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Data Progress gagal diambil');
        }
    }

    public function addAbsensi(Request $request)
    {
        try {

            $request->validate([
                'id_pekerja' => ['integer', 'required'],
                'status_absensi' => ['in:Setengah Hari,Masuk,Tidak Masuk', 'required'],
                'id_proyek' => ['integer', 'required'],
            ]);

            // Cek apakah sudah ada absensi untuk pekerja dengan id_pekerja pada tanggal hari ini
            $existingAbsensi = Absensis::where('id_pekerja', $request->id_pekerja)
                ->whereDate('tanggal', Carbon::now()->toDateString())
                ->first();

            if ($existingAbsensi) {
                // query Update 
                $existingAbsensi->update([
                    'status_absensi' => $request->status_absensi,
                ]);

                return ResponseFormatter::success($existingAbsensi, 'Berhasil diupdate');
            }

            $absensi = Absensis::create([
                'id_pekerja' => $request->id_pekerja,
                'status_absensi' => $request->status_absensi,
                'id_proyek' => $request->id_proyek,
                'tanggal' => Carbon::now()
            ]);

            return ResponseFormatter::success($absensi, 'Berhasil ditambahkan');
        } catch (Exception $error) {

            return ResponseFormatter::error($error->getMessage(), 'Gagal ditambahkan');
            // return ResponseFormatter::error('Pekerja sudah absen hari ini', 'Sudah Absen');
        }
    }

    public function getAbsensi(Request $request)
    {
        try {
            $pekerjaList = Pekerjas::with(['absensis' => function ($query) {
                $query->where('tanggal', '=', Carbon::now()->format('y-m-d'));
            }])->where('id_proyek', '=', $request->id_proyek)->get();

            return ResponseFormatter::success($pekerjaList, 'Data Absen berhasil diambil');
        } catch (Exception $error) {

            return ResponseFormatter::error($error->getMessage(), 'Data Absen gagal diambil');
        }
    }
}
