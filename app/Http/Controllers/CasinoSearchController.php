<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class CasinoSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        Log::info('Requête search reçue', $request->all());
        Log::info("INNNNNNNNNNNNNNNN");
        Log::info($request->input('query'));
        $query = $request->input('query');
        $casinos = Casino::whereRaw('LOWER(name) LIKE ?', [strtolower("%{$query}%")])
            ->orWhereRaw('LOWER(city_name) LIKE ?', [strtolower("%{$query}%")])
            ->orWhereRaw('LOWER(country_name) LIKE ?', [strtolower("%{$query}%")])
            ->orderBy('square_footage', 'desc') // Ajoute un ordre décroissant sur square_footage
            ->limit(10) // Limite les résultats à 10
            ->get(['name', 'city_name', 'country_name', 'country_title', 'city_title', 'slug', 'img_url', 'square_footage']);


        return response()->json($casinos);
    }
}
