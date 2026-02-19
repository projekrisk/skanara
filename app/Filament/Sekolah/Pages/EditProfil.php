<?php

namespace App\Filament\Sekolah\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfil extends BaseEditProfile
{
    // Mengatur judul halaman
    public function getHeading(): string
    {
        return 'Profil Saya';
    }

    // Mengatur lebar halaman agar lebih luas (sebelumnya Medium/Sempit)
    public function getMaxWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }

    // Mengganti form default dengan form kustom menggunakan TABS
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        // --- TAB 1: DATA DIRI ---
                        Tabs\Tab::make('Data Diri')
                            ->icon('heroicon-o-user')
                            ->schema([
                                // Foto Profil di tengah
                                FileUpload::make('foto_profil')
                                    ->label('Foto Profil')
                                    ->avatar()
                                    ->imageEditor()
                                    ->directory('user-photos')
                                    ->alignCenter()
                                    ->columnSpanFull(),

                                // Input Data dalam Grid 2 Kolom
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Lengkap')
                                            ->required()
                                            ->maxLength(255),
                                        
                                        TextInput::make('nip')
                                            ->label('NIP')
                                            ->numeric(),

                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->disabled() // Terkunci
                                            ->dehydrated(false),

                                        TextInput::make('peran')
                                            ->label('Jabatan')
                                            ->formatStateUsing(fn ($state) => match ($state) {
                                                'admin_sekolah' => 'Admin Sekolah',
                                                'guru' => 'Guru',
                                                'operator' => 'Operator',
                                                default => ucfirst($state),
                                            })
                                            ->disabled() // Terkunci
                                            ->dehydrated(false),
                                    ]),
                            ]),

                        // --- TAB 2: KEAMANAN (PASSWORD) ---
                        Tabs\Tab::make('Keamanan')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('password')
                                            ->label('Password Baru')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Kosongkan jika tidak ingin mengubah password.')
                                            ->rule(Password::default())
                                            ->autocomplete('new-password')
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                                        
                                        TextInput::make('password_confirmation')
                                            ->label('Ulangi Password')
                                            ->password()
                                            ->revealable()
                                            ->required(fn ($get) => filled($get('password')))
                                            ->same('password')
                                            ->dehydrated(false),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(), // Agar Tabs memenuhi lebar container
            ]);
    }
}
