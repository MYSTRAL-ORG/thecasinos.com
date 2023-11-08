<?php

namespace App\services;

use App\Models\CasinoOnline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
class GenerateJsonService
{

    public function writeJson( )
    {
        $query = " SELECT json_build_object(
    'type', 'FeatureCollection',
    'crs',  json_build_object(
        'type',      'name',
        'properties', json_build_object(
        'name', 'urn:ogc:def:crs:OGC:1.3:CRS84')),
    'features', json_agg(
        json_build_object(
            'type',       'Feature',
            'geometry',   ST_AsGeoJSON(ST_SETSRID(ST_MAKEPOINT((location_longitude)::numeric, (location_latitude)::numeric), 4326))::json,
			'id',         c.id,
            'properties', json_build_object(
                'slug', slug ,
				'name', name ,
				'imgurl', img_url,
				'opened', opened,
				'squarefootage', square_footage,
				'countryname', country_name,
				'statename', state_name,
				'cityname', city_name,
				'originalimg',has_original_img,
				'citytitle',city_title,
				'countrytitle',country_title,
				'shortdesc',cd.resume_2_words,
				'longdesc',cd.resume_1_line,
				'opened',opened,
				'always_open',always_open,
				'poker_room_name',poker_room_name,
				'poker_tables',poker_tables,
				'table_games',table_games,
				'gaming_machines',gaming_machines,
				'square_footage',square_footage,
				'hotel_name',hotel_name,
				'owners',owners,
				'cat_sportsbook',cat_sportsbook,
        		'cat_horseracing',cat_horseracing ,
				'cat_simulcasting',cat_simulcasting ,
				'cat_offtrack',cat_offtrack ,
				'cat_greyhounds',cat_greyhounds ,
				'cat_bingo',cat_bingo,
				'cat_slotmachines',cat_slotmachines ,
        		'cat_tablegames',cat_tablegames
            )
		)
    )
) AS geom FROM casino c left join casino_details cd on c.id=cd.id_casino   where cd.actif = true " ;
        $result =   DB::select($query);

        $filePath  = public_path('casinos.json');

        ;
        File::put($filePath, $result[0]->geom);


    }

}
