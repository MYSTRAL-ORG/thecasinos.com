<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\CasinoDetailsSource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class OpenAiController extends Controller
{
    public function create(Request $request, int $id) {
        $data = $request->json()->all(); // Get the JSON data from the request
        Log::info($data);

        CasinoDetailsSource::create([
            'id_casino' => $id,
            'is_done' => true,
            'source_openai_json'=>$data
        ]);

        return response()->json(['message' => 'Data processed successfully!']);
    }

    public function getListDataToCompute(){
        $lstCasinoToCompute = DB::table('casino')->whereNotNull('city_name')->whereNotIn('id',CasinoDetailsSource::where('is_done', '=',true)
            ->pluck('id_casino'))->limit(20)->get();

        return response()->json($lstCasinoToCompute);
    }



    public function getListCategoryToCompute(Request $request ){
          $categories =      Category::where('done', '=', false)->limit(50)->get();

        return response()->json($categories);
    }
    public function insertHeader(Request $request, String $countryTitle) {
        $data = $request->json()->all(); // Get the JSON data from the request
        Log::info($data);

       $categorie = Category::where('country_title', $countryTitle)->get()->first();

        $categorie->done = true;
        $categorie->footer_text =$data['content'];
        $categorie->save();
        return response()->json(['message' => 'Data processed successfully!']);
    }

}
