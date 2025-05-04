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
    public function geocodeAddress(string $address)
    {
        try {
            return Cache::remember("geocode:{$address}", now()->addDays(30), function () use ($address) {
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
                    if (!empty($data)) {
                        return [
                            'latitude' => $data[0]['lat'],
                            'longitude' => $data[0]['lon']
                        ];
                    }
                }

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }
}
