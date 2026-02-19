<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CetakKartuController extends Controller
{
    public function print(Request $request)
    {
        // Ambil ID siswa dari parameter URL ?ids=1,2,3
        $idsRaw = $request->query('ids');
        
        if (!$idsRaw) {
            abort(404, 'Tidak ada data siswa yang dipilih.');
        }

        $ids = explode(',', $idsRaw);
        
        // Ambil data siswa beserta relasi sekolah dan kelas
        $dataSiswa = Siswa::with(['sekolah', 'kelas'])
            ->whereIn('id', $ids)
            ->get();

        if ($dataSiswa->isEmpty()) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        // Return ke view khusus cetak kartu
        return view('exports.kartu_siswa', compact('dataSiswa'));
    }
}