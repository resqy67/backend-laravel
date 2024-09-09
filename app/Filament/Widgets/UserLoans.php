<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use Illuminate\Support\Facades\Auth;
use App\Models\Loan;

class UserLoans extends BaseWidget
{
    protected static ?string $heading = 'Peminjaman Buku Active';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan:: // Ambil pinjaman berdasarkan user ID saat ini
                    with(['book', 'user']) // Pastikan memuat relasi 'book' dan 'user'
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Peminjam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Judul Buku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tanggal Peminjaman')
                    ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tanggal Pengembalian')
                    ->date('Y-m-d'),
            ]);
    }
}
