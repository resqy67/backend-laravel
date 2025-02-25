<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers\BookUuidRelationManager;
use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Filament\Facades\Filament;
use App\Models\Category;
use App\Models\BookCategory;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ActionColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    // buatkan nama baru untuk navigation icon
    protected static ?string $navigationLabel = 'Buku';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Buku')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Buku')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('author')
                    ->label('Penulis')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('publisher')
                    ->label('Penerbit')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('isbn')
                    ->label('ISBN')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->label('Tahun Terbit')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pages')
                    ->label('Jumlah Halaman')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required()
                    ->directory('books/images'),
                Forms\Components\FileUpload::make('filepdf')
                    ->label('File PDF')
                    ->required()
                    ->directory('books/pdfs'),
                Forms\Components\TextInput::make('availability')
                    ->label('Ketersediaan')
                    ->required(),
                // Forms\Components\TextInput::make('loan_count')
                //     ->required()
                //     ->numeric()
                //     ->default(0),
                // Select::make('categories')
                //     ->multiple()
                //     ->relationship('categories', 'name') // Assumes `name` is the category field
                //     ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('uuid')
                //     ->label('UUID')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Buku')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('author')
                    ->label('Penulis')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('publisher')
                    ->label('Penerbit')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun Terbit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pages')
                    ->label('Jumlah Halaman')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar'),
                // Tables\Columns\TextColumn::make('categories')
                //     ->label('Kategori')
                //     ->relationship('categories', 'name')
                //     ->preload(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategori')
                    ->badge()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('categories')
                //     ->label('Kategori')
                //     ->formatStateUsing(function ($record) {
                //         // Format categories as a list of colored pills
                //         return $record->categories->map(function ($category) {
                //             return sprintf(
                //                 '<span class="inline-block px-3 py-1 text-xs font-medium text-black bg-blue-500 rounded-full">%s</span>',
                //                 e($category->name)
                //             );
                //         })->implode(' ');
                //     })
                //     ->html(), // Allow HTML in the column

                Tables\Columns\TextColumn::make('availability')
                    ->label('Ketersediaan')
                    ->numeric(),
                Tables\Columns\TextColumn::make('loan_count')
                    ->label('Jumlah Dipinjam')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                // add custom action add category with pivot table book_category
                Action::make('addCategory')
                    ->label('Add Category')
                    ->icon('heroicon-m-pencil-square')
                    ->form([
                        Forms\Components\Select::make('category_uuid')
                            ->label('Category')
                            ->placeholder('Select Category')
                            ->options(Category::all()->pluck('name', 'uuid'))
                            ->required(),
                    ])
                    ->action(function (Book $record, array $data) {
                        // Cek apakah kategori sudah ada untuk buku ini
                        if (!$record->categories()->where('category_uuid', $data['category_uuid'])->exists()) {
                            // Tambahkan kategori ke buku
                            $record->categories()->attach($data['category_uuid'], [
                                'uuid' => (string) Str::uuid(), // Generate UUID
                            ]);
                        }
                    }),
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
            // RelationManagers\BookUuidRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
