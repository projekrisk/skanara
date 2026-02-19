<?php

namespace App\Filament\Sekolah\Resources;

use App\Filament\Sekolah\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';    
    protected static ?string $modelLabel = 'Pengguna';    
    protected static ?string $slug = 'pengguna';
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Data Pengguna';
    protected static ?int $navigationSort = 3;

    // Hanya Admin Sekolah yang boleh akses
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->peran === 'admin_sekolah';
    }

    // Filter Global: Hanya tampilkan user milik sekolah ini
    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();
        $sekolahId = $tenant ? $tenant->id : auth()->user()->id_sekolah;

        return parent::getEloquentQuery()
            ->where('id_sekolah', $sekolahId)
            // Opsional: Sembunyikan akun admin sekolah sendiri agar tidak salah edit/hapus diri sendiri
            // ->where('id', '!=', auth()->id()) 
            ;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    // ID Sekolah Otomatis
                    Hidden::make('id_sekolah')
                        ->default(fn () => Filament::getTenant()?->id ?? auth()->user()->id_sekolah),

                    FileUpload::make('foto_profil')
                        ->label('Foto Profil')
                        ->directory('user-photos')
                        ->image()
                        ->avatar()
                        ->imageEditor()
                        ->circleCropper()
                        ->columnSpanFull()
                        ->alignCenter(),

                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->label('Password (Isi jika ingin ubah manual)'),

                    // Pilihan Role terbatas untuk Admin Sekolah
                    Select::make('peran')
                        ->label('Role / Peran')
                        ->options([
                            'guru' => 'Guru',
                            'operator' => 'Operator',
                            'admin_sekolah' => 'Admin Sekolah', // Opsional: jika ingin tambah admin lain
                        ])
                        ->default('guru')
                        ->required(),
                        
                    Select::make('status')
                        ->options([
                            'aktif' => 'Aktif',
                            'inactive' => 'Tidak Aktif',
                            'blokir' => 'Diblokir',
                        ])
                        ->default('aktif')
                        ->required(),
                        
                    // NIP (Khusus Guru/Admin)
                    TextInput::make('nip')
                        ->label('NIP (Opsional)')
                        ->numeric(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_profil')
                    ->label('Foto')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->copyable(),
                
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),

                TextColumn::make('peran')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin_sekolah' => 'success',
                        'guru' => 'info',
                        'operator' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'inactive' => 'warning',
                        'blokir' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('peran')
                    ->options([
                        'guru' => 'Guru',
                        'operator' => 'Operator',
                        'admin_sekolah' => 'Admin Sekolah',
                    ]),
            ])
            ->actions([
                // 1. AKSI RESET PASSWORD
                Tables\Actions\Action::make('reset_password')
                    ->label('Reset')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password')
                    ->modalDescription('Password akan diubah menjadi "Skanara123". Lanjutkan?')
                    ->action(function (User $record) {
                        $record->update([
                            'password' => Hash::make('Skanara123'),
                        ]);
                        
                        Notification::make()
                            ->title('Password Berhasil Direset')
                            ->body('Password pengguna telah diubah menjadi: Skanara123')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}