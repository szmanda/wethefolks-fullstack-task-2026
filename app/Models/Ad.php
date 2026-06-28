<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'target_url',
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function impressions()
    {
        return $this->hasMany(Impression::class);
    }
}
