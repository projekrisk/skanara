<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $slug = 'pengguna';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
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
                        ->label('Password (Isi hanya jika ingin ubah manual)'),

                    Select::make('peran')
                        ->label('Role / Peran')
                        ->options([
                            'super_admin' => 'Super Admin',
                            'admin_sekolah' => 'Admin Sekolah',
                            'guru' => 'Guru',
                            'operator' => 'Operator',
                        ])
                        ->required(),

                    Select::make('id_sekolah')
                        ->label('Sekolah')
                        ->relationship('sekolah', 'nama_sekolah')
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih Sekolah (Kosongkan jika Super Admin)'),
                        
                    Select::make('status')
                        ->options([
                            'aktif' => 'Aktif',
                            'inactive' => 'Tidak Aktif',
                            'blokir' => 'Diblokir',
                        ])
                        ->default('aktif')
                        ->required(),
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

                TextColumn::make('sekolah.nama_sekolah')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Super Admin / Global')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('peran')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
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
                        'super_admin' => 'Super Admin',
                        'admin_sekolah' => 'Admin Sekolah',
                        'guru' => 'Guru',
                        'operator' => 'Operator',
                    ]),
                Tables\Filters\SelectFilter::make('id_sekolah')
                    ->relationship('sekolah', 'nama_sekolah')
                    ->label('Filter Sekolah')
                    ->searchable(),
            ])
            ->actions([
                // 1. AKSI RESET PASSWORD (PENGGANTI EDIT)
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

                // 2. DELETE (Tetap ada)
                Tables\Actions\DeleteAction::make(),
                
                // EditAction dihapus sesuai permintaan
                // Tables\Actions\EditAction::make(),
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