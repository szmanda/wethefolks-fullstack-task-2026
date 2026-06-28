<?php

namespace App\Models;

use App\Services\UploadFileService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'path',
        'original_filename',
    ];

    protected static function booted(): void
    {
        static::deleted(function (Image $image): void {
            (new UploadFileService())->deleteImageFile($image);
        });
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
