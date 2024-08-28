<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BookCategory extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    public $timestamps = false;
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            Log::info('Creating event triggered for BookCategory');
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid(); // Generate UUID if not already set
                Log::info('UUID generated: ' . $model->uuid);
            }
        });
    }


    protected $fillable = [
        'book_uuid',
        'category_uuid'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_uuid', 'uuid');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_uuid', 'uuid');
    }
}
