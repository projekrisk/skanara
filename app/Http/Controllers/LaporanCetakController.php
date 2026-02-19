<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\HariLibur;
use App\Models\TahunAjaran;
use App\Models\JadwalHarian; // Tambahkan Import Ini
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanCetakController extends Controller
{
    public function print(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->id_sekolah) {
            abort(403, 'Akses Ditolak');
        }

        $sekolahId = $user->id_sekolah;
        $sekolah = Sekolah::find($sekolahId);
        
        $tahunAjaran = TahunAjaran::where('id_sekolah', $sekolahId)
            ->where('status_aktif', true)
            ->first();
        
        $jenis = $request->query('jenis');
        $idKelas = $request->query('id_kelas');
        
        if ($idKelas) {
            $kelasList = Kelas::with('waliKelas')->where('id', $idKelas)->get();
        } else {
            $kelasList = Kelas::with('waliKelas')->where('id_sekolah', $sekolahId)->orderBy('nama_kelas', 'asc')->get();
        }

        if ($jenis === 'harian') {
            $tanggal = $request->query('tanggal');
            return $this->printHarian($sekolah, $kelasList, $tanggal, $tahunAjaran);
        } else {
            $bulan = $request->query('bulan');
            $tahun = $request->query('tahun');
            return $this->printBulanan($sekolah, $kelasList, $bulan, $tahun, $tahunAjaran);
        }
    }
    
    public function printHarianPerKelas(Request $request) {
        return $this->print($request->merge(['jenis' => 'harian']));
    }

    private function printHarian($sekolah, $kelasList, $tanggal, $tahunAjaran)
    {
        $kelasIds = $kelasList->pluck('id');
        
        $presensiAll = Presensi::where('id_sekolah', $sekolah->id)
            ->whereDate('tanggal', $tanggal)
            ->whereHas('siswa', fn($q) => $q->whereIn('id_kelas', $kelasIds))
            ->get()
            ->keyBy('id_siswa');

        $dataPerKelas = [];
        
        foreach ($kelasList as $kelas) {
            $students = Siswa::where('id_kelas', $kelas->id)
                ->where('status', 'aktif')
                ->orderBy('nama_lengkap', 'asc')
                ->get();
            
            $siswaWithPresensi = [];
            foreach($students as $siswa) {
                $siswaWithPresensi[] = [
                    'siswa' => $siswa,
                    'presensi' => $presensiAll[$siswa->id] ?? null
                ];
            }

            $dataPerKelas[] = [
                'kelas' => $kelas,
                'data' => $siswaWithPresensi
            ];
        }
        
        $carbonDate = Carbon::parse($tanggal);
        $hariLiburRaw = HariLibur::where('id_sekolah', $sekolah->id)
            ->whereMonth('tanggal', $carbonDate->month)
            ->whereYear('tanggal', $carbonDate->year)
            ->get();
        
        return view('exports.laporan_harian', compact('sekolah', 'dataPerKelas', 'tanggal', 'tahunAjaran', 'hariLiburRaw'));
    }

    private function printBulanan($sekolah, $kelasList, $bulan, $tahun, $tahunAjaran)
    {
        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        $namaBulan = Carbon::create($tahun, $bulan)->translatedFormat('F Y');
        
        // 1. Ambil Hari Libur Nasional
        $hariLiburRaw = HariLibur::where('id_sekolah', $sekolah->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();
            
        $hariLibur = [];
        foreach($hariLiburRaw as $libur) {
            $tgl = Carbon::parse($libur->tanggal)->format('j');
            $hariLibur[$tgl] = $libur;
        }

        // 2. AMBIL JADWAL HARIAN SEKOLAH (Fix Hari Kerja)
        // Ambil kolom 'hari' (Senin, Selasa, dll) dan ubah ke lowercase agar mudah dicocokkan
        $jadwalHarianDB = JadwalHarian::where('id_sekolah', $sekolah->id)->pluck('hari')->toArray();
        // Ubah ke lowercase: ['senin', 'selasa', ...]
        $jadwalHariKerja = array_map('strtolower', $jadwalHarianDB);
        
        // Fallback: Jika jadwal kosong (belum disetting), default Senin-Jumat
        if (empty($jadwalHariKerja)) {
            $jadwalHariKerja = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        }

        // 3. Ambil Presensi
        $kelasIds = $kelasList->pluck('id');
        $presensiRaw = Presensi::where('id_sekolah', $sekolah->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereHas('siswa', fn($q) => $q->whereIn('id_kelas', $kelasIds))
            ->get();

        $presensiMap = [];
        foreach($presensiRaw as $p) {
            $tgl = Carbon::parse($p->tanggal)->format('j');
            $presensiMap[$p->id_siswa][$tgl] = $p; 
        }

        $dataPerKelas = [];
        foreach ($kelasList as $kelas) {
            $students = Siswa::where('id_kelas', $kelas->id)
                ->where('status', 'aktif')
                ->orderBy('nama_lengkap', 'asc')
                ->get();
            
            $dataPerKelas[] = [
                'kelas' => $kelas,
                'siswa' => $students
            ];
        }

        return view('exports.laporan_bulanan', compact(
            'sekolah', 'dataPerKelas', 'bulan', 'tahun', 'namaBulan', 'daysInMonth', 
            'hariLibur', 'hariLiburRaw', 'presensiMap', 'tahunAjaran',
            'jadwalHariKerja' // Kirim data jadwal ke view
        ));
    }
}