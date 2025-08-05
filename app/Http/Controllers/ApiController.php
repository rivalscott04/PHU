<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    /**
     * Get all provinces from Indonesian regions API
     */
    public function getProvinces(): JsonResponse
    {
        try {
            $response = Http::withoutVerifying()->get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
            
            if ($response->successful()) {
                $provinces = $response->json();
                return response()->json($provinces);
            } else {
                return response()->json(['error' => 'Failed to fetch provinces'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching provinces: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get cities/regencies by province ID from Indonesian regions API
     */
    public function getCities(Request $request): JsonResponse
    {
        $provinceId = $request->input('province_id');
        
        if (!$provinceId) {
            return response()->json(['error' => 'Province ID is required'], 400);
        }

        try {
            $response = Http::withoutVerifying()->get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
            
            if ($response->successful()) {
                $cities = $response->json();
                return response()->json($cities);
            } else {
                return response()->json(['error' => 'Failed to fetch cities'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching cities: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get districts by city/regency ID from Indonesian regions API
     */
    public function getDistricts(Request $request): JsonResponse
    {
        $regencyId = $request->input('regency_id');
        
        if (!$regencyId) {
            return response()->json(['error' => 'Regency ID is required'], 400);
        }

        try {
            $response = Http::withoutVerifying()->get("https://emsifa.github.io/api-wilayah-indonesia/api/districts/{$regencyId}.json");
            
            if ($response->successful()) {
                $districts = $response->json();
                return response()->json($districts);
            } else {
                return response()->json(['error' => 'Failed to fetch districts'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching districts: ' . $e->getMessage()], 500);
        }
    }
} 