<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author',
        'publisher',
        'isbn',
        'year',
        'pages',
        'coverImage',
        'filePdf',
    ];

    protected function coverImage(): Attribute
    {
        return Attribute::make(
            get: fn ($coverImage) => url('storage/books/images/' . $coverImage),
        );
    }

    protected function filePdf(): Attribute
    {
        return Attribute::make(
            get: fn ($filePdf) => url('storage/books/pdfs/' . $filePdf),
        );
    }
    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'book_categories');
    }
}
