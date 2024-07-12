<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    // public static function boot()
    // {
    //     parent::boot();
    //     // Auto generate UUID when creating data User
    //     static::creating(function ($model) {
    //         $model->uuid = Str::uuid();
    //     });
    // }
    protected $fillable = [
        'title',
        'description',
        'author',
        'publisher',
        'isbn',
        'year',
        'pages',
        'image',
        'filepdf',
        'loan_count',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/books/images/' . $image),
        );
    }

    protected function filepdf(): Attribute
    {
        return Attribute::make(
            get: fn ($filepdf) => url('/storage/books/pdfs/' . $filepdf),
        );
    }

    public function incrementLoanCount()
    {
        $this->loan_count++;
        $this->save();
    }

    // public function loans()
    // {
    //     return $this->hasMany(Loan::class, 'book_uuid', 'uuid');
    // }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_categories', 'book_uuid', 'category_uuid');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'book_uuid', 'uuid');
    }

    public function loanHistories()
    {
        return $this->hasMany(LoanHistory::class, 'book_uuid', 'uuid');
    }
}
