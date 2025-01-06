<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class GeocodeController extends Controller
{
    //@desc make request to mapbox
    //@route Get/geocode

    public function geocode(Request $request): array
    {
        $address = $request->input('address');
        $accessToken = env('MAPBOX_API_KEY');

        $response = Http::get("https://api.mapbox.com/geocoding/v5/mapbox.places/{$address}.json", [
            'access_token' => $accessToken
        ]);

        return $response->json();
    }
}
