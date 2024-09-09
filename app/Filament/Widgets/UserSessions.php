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
        // $query = $this->getUserSessionsQuery()->get();
        // dd($query); // This will dump the query result to check the data
        return $table
            ->query($this->getUserSessionsQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->formatStateUsing(function ($state) {
                        $lastActivity = Carbon::parse($state);
                        $now = Carbon::now();
                        $diffInMinutes = $lastActivity->diffInMinutes($now);
                        if ($diffInMinutes <= 5) {
                            return 'Baru Saja Login (' . $lastActivity->diffForHumans() . ')';
                        } else {
                            return $lastActivity->format('Y-m-d H:i:s');
                        }
                    })
            ]);
    }

    protected function getUserSessionsQuery()
    {
        // Ambil data pengguna berdasarkan personal access tokens yang aktif
        return User::query()
            // ->join('personal_access_tokens', 'users.id', '=', 'personal_access_tokens.tokenable_id')
            // ->select('users.*', 'personal_access_tokens.last_used_at');
            ->join('personal_access_tokens', 'users.id', '=', 'personal_access_tokens.tokenable_id')
            ->where('personal_access_tokens.tokenable_type', User::class) // Pastikan hanya mengambil token milik pengguna
            ->select('users.*', 'personal_access_tokens.updated_at'); // Pastikan last_used_at diambil
    }
}
