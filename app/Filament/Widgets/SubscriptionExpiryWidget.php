<?php

namespace App\Filament\Sekolah\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionExpiryWidget extends Widget
{
    // Arahkan ke file view yang akan kita buat
    protected static string $view = 'filament.sekolah.widgets.subscription-expiry-widget';

    // Ambil lebar penuh agar terlihat seperti banner peringatan
    protected int | string | array $columnSpan = 'full';

    // Urutan prioritas (semakin kecil semakin di atas)
    protected static ?int $sort = -10;

    public $daysLeft;
    public $isExpired = false;
    public $expiryDateFormatted;

    public function mount()
    {
        $user = Auth::user();
        
        // Pastikan user punya data sekolah
        if ($user && $user->sekolah) {
            $sekolah = $user->sekolah;
            
            if ($sekolah->langganan_berakhir_pada) {
                $expiryDate = Carbon::parse($sekolah->langganan_berakhir_pada);
                $now = Carbon::now();
                
                $this->expiryDateFormatted = $expiryDate->translatedFormat('d F Y');

                // Hitung selisih hari (float)
                // false = return nilai negatif jika tanggal sudah lewat
                $this->daysLeft = $now->diffInDays($expiryDate, false);
                
                // Jika selisih < 0, berarti sudah expired (hari ini pun dianggap warning jika jam lewat)
                if ($this->daysLeft < 0) {
                    $this->isExpired = true;
                }
            }
        }
    }

    /**
     * Tentukan kapan widget ini boleh tampil.
     */
    public static function canView(): bool
    {
        $user = Auth::user();

        // 1. Hanya tampil untuk Admin Sekolah (Guru/Operator tidak perlu lihat tagihan)
        if (!$user || $user->peran !== 'admin_sekolah') {
            return false;
        }

        $sekolah = $user->sekolah;
        
        // 2. Jika tidak ada data langganan, tidak perlu tampil (atau tampilkan warning lain)
        if (!$sekolah || !$sekolah->langganan_berakhir_pada) {
            return false;
        }

        // 3. Cek sisa hari
        $expiryDate = Carbon::parse($sekolah->langganan_berakhir_pada);
        $now = Carbon::now();
        
        // diffInDays dengan false agar bisa negatif
        $daysDiff = $now->diffInDays($expiryDate, false);

        // TAMPILKAN JIKA: Sisa waktu <= 7 hari (termasuk yang sudah minus/expired)
        return $daysDiff <= 7;
    }
}