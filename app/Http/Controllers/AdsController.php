<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Services\UploadFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use ZipArchive;

class AdsController extends Controller
{
    public function store(Request $request, UploadFileService $uploadFileService)
    {
        $validator = Validator::make($request->all(), [
            'text' => ['required', 'string', 'max:255'],
            'target_url' => ['required', 'url', 'max:2048'],
            'image' => ['required', 'image', 'max:5120'],
            'text_color' => ['required', 'regex:/^#([0-9a-fA-F]{6})$/'],
            'text_size' => ['required', 'integer', 'min:10', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($data, $request, $uploadFileService) {
            $ad = Ad::create([
                'text' => $data['text'],
                'target_url' => $data['target_url'],
            ]);

            $image = $uploadFileService->storeUploadedFile($request->file('image'), $ad);

            $baseUrl = config('app.url');
            $imageUrl = $uploadFileService->getImageUrl($image);
            $clickUrl = $baseUrl . "/api/ads/{$ad->id}/track-click";
            $impressionUrl = $baseUrl . "/api/ads/{$ad->id}/track-impression";

            $replacements = [
                '{{AD_TEXT}}' => e($ad->text),
                '{{TARGET_URL}}' => e($ad->target_url),
                '{{IMAGE_URL}}' => e($imageUrl),
                '{{TRACKING_CLICK_URL}}' => e($clickUrl),
                '{{TRACKING_IMPRESSION_URL}}' => e($impressionUrl),
                '{{TEXT_COLOR}}' => e($data['text_color']),
                '{{TEXT_SIZE}}' => e($data['text_size']),
            ];

            $htmlTemplate = file_get_contents(resource_path('ad-templates/index.html'));
            $scriptTemplate = file_get_contents(resource_path('ad-templates/scripts.js'));

            if ($htmlTemplate === false || $scriptTemplate === false) {
                return response()->json(['message' => 'Ad templates not found.'], 500);
            }

            $html = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);
            $script = str_replace(array_keys($replacements), array_values($replacements), $scriptTemplate);

            $tmpFile = tempnam(sys_get_temp_dir(), 'ad-package-');
            $zip = new ZipArchive();

            if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return response()->json(['message' => 'Unable to create ZIP archive.'], 500);
            }

            $zip->addFromString('index.html', $html);
            $zip->addFromString('scripts.js', $script);
            $zip->close();

            return response()->streamDownload(function () use ($tmpFile) {
                readfile($tmpFile);
                unlink($tmpFile);
            }, 'ad-package.zip', [
                'Content-Type' => 'application/zip',
            ]);
        });
    }

    public function trackImpression(int $id)
    {
        $ad = Ad::findOrFail($id);
        $ad->impressions()->create();

        return response()->json(['status' => 'ok'])->header('Access-Control-Allow-Origin', '*');
    }

    public function trackClick(int $id)
    {
        $ad = Ad::findOrFail($id);
        $ad->clicks()->create();

        return response()->json(['status' => 'ok'])->header('Access-Control-Allow-Origin', '*');
    }
}
