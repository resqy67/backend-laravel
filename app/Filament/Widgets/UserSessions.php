<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class UserSessions extends BaseWidget
{
    protected static ?string $heading = 'Active Users';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getUserSessionsQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last Activity')
                    ->formatStateUsing(fn($state) => Carbon::createFromTimestamp($state)->format('Y-m-d H:i:s')),
                // ->numeric(),
                // ->dateTime('Y-m-d H:i:s'),
            ]);
    }

    protected function getUserSessionsQuery()
    {
        // Ambil user_id dari sesi yang aktif
        $sessionUserIds = DB::table('sessions')
            ->where('user_id', '!=', null) // Pastikan sesi memiliki user_id
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->pluck('user_id');

        // Ambil pengguna berdasarkan user_id dari sesi
        return User::whereIn('id', $sessionUserIds);
    }
}