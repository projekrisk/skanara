<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 1. Cek apakah user adalah Admin Sekolah yang sudah login
        if ($user && $user->peran === 'admin_sekolah' && $user->sekolah) {
            
            $expireDate = $user->sekolah->langganan_berakhir_pada;
            
            // 2. Jika tanggal sudah diset DAN hari ini sudah melewatinya
            if ($expireDate && now()->greaterThan($expireDate)) {
                
                // Ambil nama rute saat ini
                $currentRoute = Route::currentRouteName();
                
                // 3. Daftar rute yang DIPERBOLEHKAN saat expired (Whitelist)
                $allowedRoutes = [
                    'filament.sekolah.pages.profil-sekolah', // Halaman untuk upgrade
                    'filament.sekolah.auth.logout',          // Logout
                    'livewire.update',                       // Wajib ada agar form Livewire berfungsi
                    'filament.sekolah.auth.login',           // Login page
                    'filament.admin.auth.login',             // Fallback
                ];

                // 4. Jika user mencoba akses rute lain selain yang diizinkan
                if (!in_array($currentRoute, $allowedRoutes)) {
                    
                    Notification::make()
                        ->title('Masa Aktif Berakhir')
                        ->body('Akses dibatasi. Silakan perpanjang paket langganan Anda di menu Profil Sekolah.')
                        ->danger()
                        ->send();

                    // FIX: Sertakan parameter 'tenant' (ID Sekolah) karena route ini berada dalam tenant panel
                    return redirect()->route('filament.sekolah.pages.profil-sekolah', [
                        'tenant' => $user->id_sekolah
                    ]);
                }
            }
        }

        return $next($request);
    }
}