<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
            if (empty($address)) {
                return null;
            }

            $cacheKey = "geocode:" . md5($address);

            return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
                $response = Http::withOptions([
                    'verify' => false
                ])->withHeaders([
                    'User-Agent' => 'EventManagementApp/1.0'
                ])->get($this->baseUrl, [
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
                    $coordinates = [
                        'latitude' => (float) $result['lat'],
                        'longitude' => (float) $result['lon']
                    ];

                    // Validate coordinates
                    if (!$this->validateCoordinates($coordinates)) {
                        return null;
                    }

                    return $coordinates;
                }

                return null;
            });
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate coordinates are within reasonable bounds
     *
     * @param array $coordinates
     * @return bool
     */
    protected function validateCoordinates(array $coordinates): bool
    {
        if (!isset($coordinates['latitude']) || !isset($coordinates['longitude'])) {
            return false;
        }

        $lat = $coordinates['latitude'];
        $lon = $coordinates['longitude'];

        // Check if coordinates are within valid ranges
        return $lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180;
    }
}
