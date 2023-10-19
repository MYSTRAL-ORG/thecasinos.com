<?php

namespace App\services;
use App\Models\Casino;
use App\Models\CasinoDetailsSource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OpenAiService
{
    public static string $urlOpenAi= "https://api.openai.com/v1/chat/completions";

    public function storeResponseFromChatGPT()
    {
        Casino::all()->where('slug', 'luxor-hotel-and-casino-2686')->each(function ($casino) {
            $casinoInfo =  $casino->name ;
            $casinoCity = $casino->city_name ?? $casino->state_name ?? $casino->country_name;

            if($casinoCity != null){
                                $casinoInfo = $casinoInfo ." , ".$casinoCity;
            }

        $listCasinosFeatures = $this->getListFaetures($casino);

            $prompt = 'I Need You To Act As A Novel Writer Exceptionally Talented SEO Writes Flawlessly In English. Write In Your Own Words Rather Than Copying And Pasting From Other Sources. Consider perplexity and burstiness when creating content, ensuring high levels of both without sacrificing specificity or context. Use fully detailed paragraphs that captivate the reader. Write In A Conversational Style As If Written By A Human (Employ An Informal Tone, Utilize Personal Pronouns, Engage The Reader, Keep It Simplified, Use The Active Voice, Keep It Brief, Use Rhetorical Questions, and Embed Analogies And Metaphors).
Start with a catchy title for a story ($Casino_title).
Now write a positive story about '.$casinoInfo.' in the F. Scott Fitzgerald style without mentioning him and giving all the informations about the casino in the story, here the main datas of the casino that will help you write your novel :
– Casino opened : '.$casino->opened.'
– Always opened : '.($casino->always_open ? 'Yes' : 'No').'
– Poker Room Name : '.$casino->poker_room_name.'
– Poker Tables: '.$casino->poker_tables.'
– Table Games: '.$casino->table_games.'
– Gaming machines: '.$casino->gaming_machines.'
– Casino Square: '.$casino->square_footage.'
– Hotel name : '.$casino->hotel_name.'
– Owners: '.$casino->owners.'
– Game categories: '.$listCasinosFeatures.'
Following the novel I would like you to create 4 paragraphs presenting this casino:
– $Casino_Sumup
– $Casino_Games
– $Casino_Fun_Facts
To finish I would like a short one-line summary of the casino and a 2-word summary (exemple : Elegance & )
– $Casino_Resume_1_line
– $Casino_Resume_2_words';


            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.config('app.openai_api_key'),
            ])->timeout(120)->post($this::$urlOpenAi, [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 1,
                'max_tokens' => 1000,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,

            ]);

            $sourceJson = $response->json();
            dd($sourceJson);
            $data2Insert = $sourceJson['choices'][0]['message']['content'];


            CasinoDetailsSource::create([
                'id_casino' => $casino->id,
                'is_done' => true,
                'source_openai' => $data2Insert
            ]);
        });

    }

    private function getListFaetures($casino)
    {
       $gameListString = [];
        $casino->cat_poker ? array_push($gameListString,"Poker"):"";
        $casino->cat_sportsbook ? array_push($gameListString,"Sportsbook"):"";
        $casino->cat_horseracing ? array_push($gameListString,"Horse racing"):"";
        $casino->cat_simulcasting ? array_push($gameListString,"Simulcast"):"";
        $casino->cat_offtrack ? array_push($gameListString,"Offtrack"):"";
        $casino->cat_greyhounds ? array_push($gameListString,"Greyhounds"):"";
        $casino->cat_bingo ? array_push($gameListString,"Bingo"):"";
        $casino->cat_slotmachines ? array_push($gameListString,"Slot Machines"):"";
        $casino->cat_tablegames ? array_push($gameListString,"Table games"):"";

            return implode(",",$gameListString);



    }

}
