<?php

namespace App\Http\Controllers\API;

use App\Models\Proyeks;
use App\Models\Pengeluarans;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
}
