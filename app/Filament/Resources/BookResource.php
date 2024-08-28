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
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('author')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('publisher')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('isbn')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pages')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('books/images'),
                Forms\Components\FileUpload::make('filepdf')
                    ->directory('books/pdfs'),
                Forms\Components\TextInput::make('availability')
                    ->required(),
                Forms\Components\TextInput::make('loan_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('categories')
                    ->multiple()
                    ->relationship('categories', 'name') // Assumes `name` is the category field
                    ->preload(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('author')
                    ->searchable(),
                Tables\Columns\TextColumn::make('publisher')
                    ->searchable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pages')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                // Tables\Columns\TextColumn::make('filepdf')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('availability')
                    ->label('Ketersediaan')
                    ->numeric(),
                Tables\Columns\TextColumn::make('loan_count')
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
