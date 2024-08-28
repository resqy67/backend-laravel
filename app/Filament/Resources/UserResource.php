<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Pengguna';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nisn')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('avatar')
                    ->image()
                    ->directory('users/avatars')
                    ->visibility('public'),
                Forms\Components\Select::make('class')
                    ->options([
                        'X TKJ 1' => 'X TKJ 1',
                        'X TKJ 2' => 'X TKJ 2',
                        'X MPLB 1' => 'X MPLB 1',
                        'X MPLB 2' => 'X MPLB 2',
                        'X PPLG' => 'X PPLG',
                        'X DKV' => 'X DKV',
                        'X AKL' => 'X AKL',
                        'XI TKJ' => 'XI TKJ',
                        'XI MPLB 1' => 'XI MPLB 1',
                        'XI MPLB 2' => 'XI MPLB 2',
                        'XI PPLG' => 'XI PPLG',
                        'XI DKV 1' => 'XI DKV 1',
                        'XI DKV 2' => 'XI DKV 2',
                        'XI AKL' => 'XI AKL',
                        'XII TKJ 1' => 'XII TKJ 1',
                        'XII TKJ 2' => 'XII TKJ 2',
                        'XII MPLB 1' => 'XII MPLB 1',
                        'XII MPLB 2' => 'XII MPLB 2',
                        'XII PPLG' => 'XII PPLG',
                        'XII DKV 1' => 'XII DKV 1',
                        'XII DKV 2' => 'XII DKV 2',
                        'XII AKL' => 'XII AKL',
                    ])
                    ->required(),
                // Forms\Components\TextInput::make('class')
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\Select::make('description')
                    ->options([
                        'Seorang Siswa' => 'Seorang Siswa',
                        'Seorang Guru' => 'Seorang Guru',
                        'Seorang Karyawan' => 'Seorang Karyawan',
                    ])
                    ->required(),
                // Forms\Components\TextInput::make('description')
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')

                    ->sortable(),
                // Tables\Columns\TextColumn::make('avatar')
                //     ->searchable(),
                Tables\Columns\ImageColumn::make('avatar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('class')
                    ->label('Kelas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
