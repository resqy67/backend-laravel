<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    public $timestamps = false;
    // public static function boot()
    // {
    //     parent::boot();
    //     // Auto generate UUID when creating data User
    //     static::creating(function ($model) {
    //         $model->id = Str::uuid();
    //     });
    // }

    protected $fillable = [
        'name'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_categories', 'category_uuid', 'book_uuid');
    }
}
