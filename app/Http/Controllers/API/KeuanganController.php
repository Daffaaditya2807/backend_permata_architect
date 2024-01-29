<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\Pengeluarans;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Pemasukans;
use Illuminate\Support\Facades\Storage;

class KeuanganController extends Controller
{
    //Fungsi Tambah Pengeluaran
    public function addPengeluaran(Request $request)
    {
        try {
            $request->validate([
                'judul_pengeluaran' => ['required', 'string', 'max:255'],
                'keterangan' => ['required', 'string', 'max:1000'],
                'sumber_dana' => ['required', 'string', 'max:255'],
                'id' => ['required'],
                'id_proyek' => ['required'],
                'satuan' => ['required', 'string'],
                'quantity' => ['required', 'int'],
                'harga_satuan' => ['required', 'int'],
                'vendor' => ['required', 'string', 'max:255'],
                'image' => ['required', 'image', 'max:2048']
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $timestamp = Carbon::now()->format('YmdHis');
                $filename = 'Pengeluaran_' . $timestamp . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/images/pengeluaran_image', $filename);
                $picUrl = Storage::url($path);
            } else {
                $picUrl = null;
            }

            $hargaSatuan = $request->harga_satuan;
            $quantity = $request->quantity;

            $totalHarga =  $hargaSatuan * $quantity;

            $pengeluaran = Pengeluarans::create([
                'judul_pengeluaran' => $request->judul_pengeluaran,
                'keterangan' => $request->keterangan,
                'sumber_dana' => $request->sumber_dana,
                'id' => $request->id,
                'id_proyek' => $request->id_proyek,
                'satuan' => $request->satuan,
                'quantity' =>  $quantity,
                'harga_satuan' =>  $hargaSatuan,
                'total_pengeluaran' => $totalHarga,
                'vendor' => $request->vendor,
                'foto' => $picUrl
            ]);

            return ResponseFormatter::success($pengeluaran, 'Berhasil ditambahkan');
        } catch (Exception $error) {
            return ResponseFormatter::success($error->getMessage(), 'Gagal ditambahkan');
        }
    }

    //Fungsi Tambah Pemasukan
    public function addPemasukan(Request $request)
    {
        try {
            $request->validate([
                'jumlah_pemasukan' => ['required', 'int'],
                'keterangan' => ['required', 'string', 'max:1000'],
                'sumber_dana' => ['required', 'string', 'max:255'],
                'id' => ['required'],
                'image' => ['required', 'image', 'max:2048']
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $timestamp = Carbon::now()->format('YmdHis');
                $filename = 'Pemasukan' . $timestamp . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/images/Pemasukanimage', $filename);
                $picUrl = Storage::url($path);
            } else {
                $picUrl = null;
            }

            $pemasukan = Pemasukans::create([
                'jumlah_pemasukan' => $request->jumlah_pemasukan,
                'keterangan' => $request->keterangan,
                'sumber_dana' => $request->sumber_dana,
                'id' => $request->id,
                'foto' => $picUrl
            ]);

            return ResponseFormatter::success($pemasukan, 'Berhasil ditambahkan');
        } catch (Exception $error) {
            return ResponseFormatter::success($error->getMessage(), 'Gagal ditambahkan');
        }
    }
}
