<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'image',
        'filePdf',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/books/images/' . $image),
        );
    }

    protected function filePdf(): Attribute
    {
        return Attribute::make(
            get: fn ($filePdf) => url('/storage/books/pdfs/' . $filePdf),
        );
    }
}
