<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadFileService
{
    public function storeUploadedFile(UploadedFile $file, Ad $ad): Image
    {
        $path = Storage::disk('public')->putFile('ads/images', $file);

        return Image::create([
            'ad_id' => $ad->id,
            'path' => $path,
            'original_filename' => $file->getClientOriginalName(),
        ]);
    }

    public function getImageUrl(Image $image): string
    {
        return Storage::disk('public')->url($image->path);
    }

    public function deleteImageFile(Image $image): bool
    {
        return Storage::disk('public')->delete($image->path);
    }
}
