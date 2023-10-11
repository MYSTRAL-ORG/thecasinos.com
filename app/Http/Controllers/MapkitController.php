<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MapkitController extends Controller
{
    public function fetchMapData(): string
    {
        $client = new Client();
        $url = config('app.mapkit_url'); // Récupérer l'URL depuis la configuration
        Log::info($url);
        $apiKey = config('app.mapkit_api_key'); // Récupérer la clé d'API depuis la configuration
        Log::info($apiKey);
        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
        ];

       // $token = "{\"region\":\"default\",\"attributions\":[{\"attributionId\":\"2310043\",\"global\":[{\"name\":\"\u200e\",\"url\":\"https:\/\/gspe21-ssl.ls.apple.com\/html\/attribution-264.html\"},{\"name\":\"MMI\",\"url\":\"https:\/\/gspe21-ssl.ls.apple.com\/html\/attribution-264.html\"},{\"name\":\"\u00a9 GeoTechnologies, Inc.\",\"url\":\"https:\/\/gspe21-ssl.ls.apple.com\/html\/attribution-264.html\"}],\"regional\":[]}],\"modes\":{\"hybrid\":{\"layers\":[{\"tileSource\":\"satellite\",\"lowResTileSource\":\"satellite\",\"allowPrefetchingLowResAtZDiffs\":[-8,-6,-3],\"maximumOverdrawScale\":2},{\"tileSource\":\"hybrid-overlay\",\"allowPrefetchingLowResAtZDiffs\":[-8,-6,-3],\"maximumOverdrawScale\":1}]},\"satellite\":{\"layers\":[{\"tileSource\":\"satellite\",\"lowResTileSource\":\"satellite\",\"allowPrefetchingLowResAtZDiffs\":[-8,-6,-3],\"maximumOverdrawScale\":2}]},\"standard\":{\"layers\":[{\"tileSource\":\"standard\",\"lowResTileSource\":\"standard\",\"allowPrefetchingLowResAtZDiffs\":[-8,-6,-3],\"maximumOverdrawScale\":2}]}},\"mapEngine\":\"apple\",\"environment\":\"MapsWebProd\",\"analytics\":{\"analyticsUrl\":\"https:\/\/gsp10.apple-mapkit.com\/mw\/v1\/reportAnalytics\",\"errorUrl\":\"https:\/\/gsp10.apple-mapkit.com\/mw\/v1\/reportError\"},\"madabaDomains\":[\"cdn1.apple-mapkit.com\",\"cdn2.apple-mapkit.com\",\"cdn3.apple-mapkit.com\",\"cdn4.apple-mapkit.com\"],\"apiBaseUrl\":\"https:\/\/api.apple-mapkit.com\/v1\/\",\"madabaBaseUrl\":\"https:\/\/cdn1.apple-mapkit.com\",\"tileSources\":[{\"attributionId\":\"2310043\",\"tileSource\":\"standard\",\"supportedSizes\":[0,1,2],\"supportedResolutions\":[1,2,3],\"supportedLanguages\":[\"ar\",\"ca\",\"cs\",\"da\",\"de\",\"el\",\"en\",\"en-AU\",\"en-GB\",\"es\",\"es-MX\",\"es-US\",\"fi\",\"fr\",\"fr-CA\",\"he\",\"hi\",\"hr\",\"hu\",\"id\",\"it\",\"ja\",\"ko\",\"ms\",\"nb\",\"nl\",\"pl\",\"pt\",\"pt-PT\",\"ro\",\"ru\",\"sk\",\"sv\",\"th\",\"tr\",\"uk\",\"vi\",\"zh-Hans\",\"zh-Hant\",\"zh-HK\"],\"minZoomLevel\":2,\"maxZoomLevel\":24,\"showPrivacyLink\":true,\"showTermsOfUseLink\":true,\"domains\":[\"cdn1.apple-mapkit.com\",\"cdn2.apple-mapkit.com\",\"cdn3.apple-mapkit.com\",\"cdn4.apple-mapkit.com\"],\"needsLocationShift\":false,\"path\":\"\/ti\/tile?style=0&size={{tileSizeIndex}}&x={{x}}&y={{y}}&z={{z}}&scale={{resolution}}&lang={{lang}}&v=2310043&poi=0&accessKey=1696508891_4497879655830229496_%2F_ahzOVEIMstHtSyPGxNPDc7%2BuBWGoCgavev%2BehoNnZoY%3D\"},{\"attributionId\":\"2310043\",\"tileSource\":\"hybrid-overlay\",\"supportedSizes\":[0,1,2],\"supportedResolutions\":[1,2],\"minZoomLevel\":2,\"maxZoomLevel\":19,\"showPrivacyLink\":true,\"showTermsOfUseLink\":true,\"domains\":[\"cdn1.apple-mapkit.com\",\"cdn2.apple-mapkit.com\",\"cdn3.apple-mapkit.com\",\"cdn4.apple-mapkit.com\"],\"needsLocationShift\":false,\"path\":\"\/ti\/tile?style=46&size={{tileSizeIndex}}&x={{x}}&y={{y}}&z={{z}}&scale={{resolution}}&lang={{lang}}&v=2310043&poi=0&accessKey=1696508891_4497879655830229496_%2F_ahzOVEIMstHtSyPGxNPDc7%2BuBWGoCgavev%2BehoNnZoY%3D\"},{\"attributionId\":\"2310043\",\"tileSource\":\"satellite\",\"supportedSizes\":[0,1,2],\"supportedResolutions\":[1],\"minZoomLevel\":3,\"maxZoomLevel\":19,\"showPrivacyLink\":true,\"showTermsOfUseLink\":true,\"domains\":[\"sat-cdn1.apple-mapkit.com\",\"sat-cdn2.apple-mapkit.com\",\"sat-cdn3.apple-mapkit.com\",\"sat-cdn4.apple-mapkit.com\"],\"needsLocationShift\":false,\"path\":\"\/tile?style=7&size={{tileSizeIndex}}&scale={{resolution}}&z={{z}}&x={{x}}&y={{y}}&v=9542&accessKey=1696508891_4497879655830229496_%2F_ahzOVEIMstHtSyPGxNPDc7%2BuBWGoCgavev%2BehoNnZoY%3D\"}],\"disableCsr\":false,\"accessKey\":\"1696508891_4497879655830229496_\/_ahzOVEIMstHtSyPGxNPDc7+uBWGoCgavev+ehoNnZoY=\",\"expiresInSeconds\":1800,\"showWordmarkLogo\":true,\"authInfo\":{\"access_token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJtYXBzYXBpIiwidGlkIjoiVjc4TVM4VlJKNiIsImFwcGlkIjoiVjc4TVM4VlJKNi5tYXBzLmNvbS50aGVjYXNpbm9zIiwiaXRpIjpmYWxzZSwiaXJ0IjpmYWxzZSwiaWF0IjoxNjk2NTA3MDkxLCJleHAiOjE2OTY1MDg4OTF9.6A3AvXJ24oMgwulFdtDXas6ECoX-N_jo_2xHpGhK1IV1O7vE8oilQ27Xj1DBCy66waHFYPWmkAiAjVGZcYwOsA\",\"expires_in\":1800,\"team_id\":\"V78MS8VRJ6\"},\"countryCode\":\"RE\"}";


        try {
            $response = $client->get($url, ['headers' => $headers]);

            $JSONObject = json_decode(stripslashes($response->getBody()->getContents()),true);

            $accessKey =  $JSONObject["accessKey"];
            Log::info("KEY: ");
            Log::info($accessKey);
            return view('welcome', compact('accessKey', ));

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
