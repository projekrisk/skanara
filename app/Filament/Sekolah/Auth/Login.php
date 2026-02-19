<?php

namespace App\Filament\Sekolah\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;

class Login extends BaseLogin
{
    // Override fungsi validasi kredensial
    protected function getCredentialsFromFormData(array $data): array
    {
        $credentials = parent::getCredentialsFromFormData($data);
        
        $email = $credentials['email'];
        $user = \App\Models\User::where('email', $email)->first();

        // Cek apakah user ada TAPI statusnya belum aktif
        if ($user && $user->status !== 'aktif' && $user->peran !== 'super_admin') {
            throw ValidationException::withMessages([
                'data.email' => __('Akun Anda belum diaktivasi. Silakan cek email atau hubungi admin.'),
            ]);
        }

        return $credentials;
    }
}