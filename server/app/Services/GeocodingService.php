<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    // The base URL for the OpenStreetMap Nominatim geocoding API
    protected $baseUrl = 'https://nominatim.openstreetmap.org/search';

    /**
     * Convert an address into geographic coordinates (latitude and longitude).
     *
     * @param string $address
     * @return array|null
     */
    public function geocodeAddress(string $address): ?array
    {
        try {
            $cacheKey = "geocode:" . md5($address);

            return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
                $response = Http::withOptions([
                    'verify' => false
                ])->withHeaders([
                    'User-Agent' => 'EventManagementApp/1.0'
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (empty($data)) {
                        return null;
                    }

                    $result = $data[0];
                    return [
                        'latitude' => (float) $result['lat'],
                        'longitude' => (float) $result['lon']
                    ];
                }

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Geocoding error', [
                'message' => $e->getMessage()
            ]);

            return null;
        }
    }
}
