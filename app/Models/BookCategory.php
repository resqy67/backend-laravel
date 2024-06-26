<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookCategory extends Model
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
    //         $model->id = Str::uuid();
    //     });
    // }


    protected $fillable = [
        'book_uuid',
        'category_uuid'
    ];
}
