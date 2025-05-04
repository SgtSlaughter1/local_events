<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function getForecast(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'date' => 'required|date'
        ]);

        $coordinates = $this->geocodingService->geocodeAddress($request->address);

        if (!$coordinates) {
            return response()->json(['error' => 'Could not geocode address'], 400);
        }

        $cacheKey = "weather:{$coordinates['latitude']}:{$coordinates['longitude']}:{$request->date}";

        try {
            return Cache::remember($cacheKey, now()->addHours(1), function () use ($coordinates, $request) {
                $response = Http::withOptions([
                    'verify' => false
                ])->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                    'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_probability_max',
                    'timezone' => 'auto',
                    'start_date' => $request->date,
                    'end_date' => $request->date
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return response()->json(['error' => 'Could not fetch weather data'], 500);
            });
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching weather data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
