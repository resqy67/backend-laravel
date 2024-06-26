<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'book_uuid',
        'status',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_uuid ', 'uuid');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'book_copy_uuid', 'uuid');
    }
}
