<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class SiswaImport implements ToModel, WithHeadingRow
{
    protected $idSekolah;
    protected $kelasCache = [];

    public function __construct($idSekolah)
    {
        $this->idSekolah = $idSekolah;
    }

    public function model(array $row)
    {
        $nis = isset($row['nis']) ? trim((string)$row['nis']) : null;
        $namaLengkap = $row['nama_lengkap'] ?? null;
        $namaKelasString = $row['kelas'] ?? null;
        $noHp = $row['no_hp'] ?? null;
        if (empty($nis) || empty($namaLengkap)) {
            return null; 
        }
        $idKelas = $this->getKelasId($namaKelasString);
        
        if (!$idKelas) {
            Log::error("Import Gagal NIS $nis: Kelas '$namaKelasString' tidak ditemukan di database.");
            return null; 
        }

        $siswa = Siswa::where('id_sekolah', $this->idSekolah)
            ->where('nis', $nis)
            ->first();

        $dataSimpan = [
            'nama_lengkap'  => $namaLengkap,
            'nisn'          => $row['nisn'] ?? null,
            'id_kelas'      => $idKelas,
            'jenis_kelamin' => isset($row['jenis_kelamin']) ? strtoupper(trim($row['jenis_kelamin'])) : 'L',
            'nomor_hp_ortu' => $noHp,
            'status'        => 'aktif',
        ];

        if ($siswa) {
            $siswa->update($dataSimpan);
            return $siswa;
        } else {
            return new Siswa(array_merge([
                'id_sekolah' => $this->idSekolah,
                'nis'        => $nis,
            ], $dataSimpan));
        }
    }

    private function getKelasId($namaKelas)
    {
        if (empty($namaKelas)) return null;

        if (isset($this->kelasCache[$namaKelas])) {
            return $this->kelasCache[$namaKelas];
        }

        $kelas = Kelas::where('id_sekolah', $this->idSekolah)
            ->where('nama_kelas', $namaKelas)
            ->first();

        if (!$kelas) {
            $kelas = Kelas::where('id_sekolah', $this->idSekolah)
                ->where('nama_kelas', 'LIKE', $namaKelas)
                ->first();
        }

        if (!$kelas) {
            $normalizedName = preg_replace('!\s+!', ' ', $namaKelas);
            $kelas = Kelas::where('id_sekolah', $this->idSekolah)
                ->where('nama_kelas', $normalizedName)
                ->first();
        }

        if ($kelas) {
            $this->kelasCache[$namaKelas] = $kelas->id;
            return $kelas->id;
        }

        return null;
    }
}