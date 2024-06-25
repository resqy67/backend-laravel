<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $fillable = [
        'CategoryName'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_categories');
    }
}
