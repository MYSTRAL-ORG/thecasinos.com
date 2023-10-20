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
Now write a positive novel from today about the experience of a player who discovers the different aspects of the casino : '.$casinoInfo.' in the F. Scott Fitzgerald style without mentioning him and giving all the informations below about the casino in the story, here the main datas of the casino that will help you write your novel :
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


/*
$rep = 'Title: "In The Shadows Of The Luxor: A Gamers Tryst With Luck"\n
\n
There was a certain brilliance to the atmosphere on the first of April in 1992. The fervor of a man stepping into a casino, in the heart of a city where the lights never dim out, seemed to hold him in an ecstatic vice. As he strode into the Luxor Casino in Las Vegas, his feet echoing the unabated rhythm of chance, he was met with the timeless allure that only the clink of chips and the whir of slot machines could provide. His pulse quickened at the realization; he was always in the game now.\n
\n
His eyes took in the boundless expanse of the gaming floor, an impressive 120,000 square feet of opportunity. His gaze was immediately drawn to the eleven poker tables, each reminding him of a knights round table. It was here that wars were waged on felt battlegrounds, and where, like some noble order, the Luxor Poker Room was bestowed its title. A sense of exhilaration filled him, for he knew that the game of poker required not just a mind for strategy, but the unfaltering resolve to stare down luck with each turn of the card.\n
            \n
The matchless thrill of the sporting gamble had opened up a new window of excitement within him. As he wandered past the mind-bending variety of 62 table games and over a thousand gaming machines, he couldnt help but marvel at the fetching draw of them all. In the realm of the Luxor Casino, contests of fortune in sportsbooks intertwined with the rhapsodic melody and flashing lights of slot machines, effectively reflecting his diverse craving for challenge.\n
\n
As the sun dawned on another day, he found that his odyssey of chance had not ebbed, as it did in elsewhere. His heart swelled with avidity when he realized that at the Luxor, the games never cease. Nightfall or daybreak, the dice never stopped rolling; the thrill never abated. The Luxor Casino truly was a gamblers sanctuary where the epoch of stake never slept.\n
            \n
$Casino_Sumup:\n
Unveiled in the Spring of 1992, the Luxor Casino, owned by VICI Properties Inc., harbors a relentless spirit of thrill and playfulness. Altogether, located in the hub of entertainment, this gaming heaven spans around 120,000 square feet filled with a medley of casino games that never close.\n
            \n
$Casino_Games:\n
The Luxor Casino, boasts 11 grand poker tables in the heralded Luxor Poker Room, a haven for the tacticians. Beyond that, it houses an enchanting array of gaming machines and table games reaching numbers of 1100 and 62 respectively. For those inclined towards sports, the Sportsbook offers endless ingredients of fun.\n
            \n
$Casino_Fun_Facts:\n
Owned by VICI properties, the Luxor Las Vegas casino isn’t just a gaming hub, it s a world of its own. From the eternally open gaming floors to the relentless Tattoo at Luxor, it embraces novelty and tradition both. Sportsbooks, Poker, Slot machines all find a home here.\n
\n
$Casino_Resume_1_line:\n
Luxor Casino: Las Vegas’ enduring emblem of audacious play and ceaseless excitement.\n
\n
$Casino_Resume_2_words:\n
Timelessness & Thrill.';*/








           $sourceJson = $response->json();
            Log::info($sourceJson);
            $data2Insert = $sourceJson['choices'][0]['message']['content'];



            //split string $rep  into array of lines
            $lines = explode("\n", $data2Insert);
            //in each $lines remove the carateres "\n"


            $filteredArray = array_filter($lines, 'trim');
            //remove empty lines
            $redefinedArray = [];
            $counter = 0;
            foreach ( $filteredArray as $value) {
                $redefinedArray[strval($counter)] = $value;
                $counter++;
            }


            $filteredArray = array_filter($lines, 'trim');
            //remove empty lines


            CasinoDetailsSource::create([
                'id_casino' => $casino->id,
                'is_done' => true,
                'source_openai' => $data2Insert,
                'source_openai_json'=>$redefinedArray
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
