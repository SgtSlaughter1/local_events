<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class WeatherController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function getForecast(Request $request)
    {
        try {
            $request->validate([
                'city' => 'required|string',
                'date' => 'required|date'
            ]);

            $coordinates = $this->geocodingService->geocodeAddress($request->city);

            if (!$coordinates) {
                return response()->json(['error' => 'Could not geocode city'], 400);
            }

            // Format date to YYYY-MM-DD
            $formattedDate = Carbon::parse($request->date)->format('Y-m-d');

            $cacheKey = "weather:{$coordinates['latitude']}:{$coordinates['longitude']}:{$formattedDate}";

            return Cache::remember($cacheKey, now()->addHours(1), function () use ($coordinates, $formattedDate) {
                $response = Http::withOptions([
                    'verify' => false
                ])->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'EventManagementApp/1.0'
                ])->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                    'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_probability_max',
                    'timezone' => 'auto',
                    'start_date' => $formattedDate,
                    'end_date' => $formattedDate
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return response()->json([
                    'error' => 'Could not fetch weather data',
                    'status' => $response->status(),
                    'message' => $response->body()
                ], 500);
            });
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching weather data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
