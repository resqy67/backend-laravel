<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Book;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage; // Add this line to import the missing class

use function Livewire\wrap;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationLabel = 'Peminjaman Buku';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('book_uuid')
                    ->required()
                    ->maxLength(36),
                Forms\Components\DatePicker::make('loan_date')
                    ->required(),
                Forms\Components\DatePicker::make('return_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('uuid')
                //     ->label('UUID')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->wrap()
                    // ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Nama Buku')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tanggal Pinjam')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tanggal Kembali')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('returnBook')
                    ->label('Return Book')
                    ->action(function (Loan $record) {
                        // Dapatkan buku terkait
                        $book = Book::where('uuid', $record->book_uuid)->first();

                        if ($book) {
                            // Tambahkan availability setelah buku dikembalikan
                            $book->availability += 1;
                            $book->save();

                            // Hapus record loan setelah buku dikembalikan
                            $record->delete();

                            // Kirim notifikasi ke user
                            self::sendNotification($record);

                            return redirect()->back()->with('success', 'Book returned successfully');
                        } else {
                            return redirect()->back()->with('error', 'Book not found');
                        }
                    })
                    ->requiresConfirmation(),
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }

    protected static function sendNotification(Loan $loan)
    {
        $messaging = Firebase::messaging();
        $user = $loan->user;
        if ($user->token_fcm) {
            try {
                $message = CloudMessage::withTarget('token', $user->token_fcm)
                    ->withNotification([
                        'title' => 'Book Returned',
                        'body' => 'Your book has been returned successfully'
                    ]);
                $messaging->send($message);
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                // Handle exception
                $user->token_fcm = null;
                $user->save();
            } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
                // Handle exception
            }
        } else {
            // Handle exception
        }
    }
}
