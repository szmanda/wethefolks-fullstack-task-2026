<?php

namespace App\Http\Controllers;

use App\Models\Ad;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $ads = Ad::withCount(['clicks', 'impressions'])
            ->latest()
            ->get();

        return response()->json([
            'summary' => [
                'total_ads' => $ads->count(),
                'total_clicks' => $ads->sum('clicks_count'),
                'total_impressions' => $ads->sum('impressions_count'),
            ],
            'ads' => $ads->map(function (Ad $ad) {
                return [
                    'id' => $ad->id,
                    'text' => $ad->text,
                    'target_url' => $ad->target_url,
                    'clicks' => $ad->clicks_count,
                    'impressions' => $ad->impressions_count,
                    'created_at' => $ad->created_at?->toIso8601String(),
                ];
            }),
        ]);
    }
}