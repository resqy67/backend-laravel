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
                Loan::where('user_id', Auth::id())->with('book')->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Book Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Loan Date')
                    ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Return Date')
                    ->date('Y-m-d'),
            ]);
    }
}
