<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Casino
 *
 * @property int $id
 * @property string|null $id_source
 * @property string|null $name
 * @property string|null $slug
 * @property Carbon|null $opened
 * @property int|null $gaming_machines
 * @property int|null $poker_tables
 * @property int|null $square_footage
 * @property string|null $country_name
 * @property string|null $country_title
 * @property string|null $country_iso_code
 * @property string|null $state_name
 * @property string|null $state_title
 * @property string|null $city_name
 * @property string|null $city_title
 * @property float|null $location_longitude
 * @property float|null $location_latitude
 * @property bool|null $restaurants
 * @property bool|null $venues
 * @property bool|null $hotels
 * @property bool|null $shops
 * @property bool|null $spas
 * @property bool|null $cat_casino
 * @property bool|null $cat_poker
 * @property bool|null $cat_sportsbook
 * @property bool|null $cat_horseracing
 * @property bool|null $cat_simulcasting
 * @property bool|null $cat_offtrack
 * @property bool|null $cat_greyhounds
 * @property bool|null $jaialai
 * @property bool|null $cat_bingo
 * @property bool|null $cat_slotmachines
 * @property bool|null $cat_tablegames
 * @property bool|null $is_scrap
 * @property string|null $address
 * @property string|null $telephone
 * @property string|null $website
 * @property string|null $facebook
 * @property string|null $twitter
 * @property string|null $toll_free
 * @property string|null $img_url
 * @property bool|null $has_original_img
 *
 * @package App\Models
 */
class Casino extends Model
{
	protected $table = 'casino';
	public $timestamps = false;

	protected $casts = [
		'opened' => 'datetime',
		'gaming_machines' => 'int',
		'poker_tables' => 'int',
		'square_footage' => 'int',
		'location_longitude' => 'float',
		'location_latitude' => 'float',
		'restaurants' => 'bool',
		'venues' => 'bool',
		'hotels' => 'bool',
		'shops' => 'bool',
		'spas' => 'bool',
		'cat_casino' => 'bool',
		'cat_poker' => 'bool',
		'cat_sportsbook' => 'bool',
		'cat_horseracing' => 'bool',
		'cat_simulcasting' => 'bool',
		'cat_offtrack' => 'bool',
		'cat_greyhounds' => 'bool',
		'jaialai' => 'bool',
		'cat_bingo' => 'bool',
		'cat_slotmachines' => 'bool',
		'cat_tablegames' => 'bool',
		'is_scrap' => 'bool',
        'has_original_img' => 'bool'

	];

	protected $fillable = [
		'id_source',
		'name',
		'slug',
		'opened',
		'gaming_machines',
		'poker_tables',
		'square_footage',
		'country_name',
		'country_title',
		'country_iso_code',
		'state_name',
		'state_title',
		'city_name',
		'city_title',
		'location_longitude',
		'location_latitude',
		'restaurants',
		'venues',
		'hotels',
		'shops',
		'spas',
		'cat_casino',
		'cat_poker',
		'cat_sportsbook',
		'cat_horseracing',
		'cat_simulcasting',
		'cat_offtrack',
		'cat_greyhounds',
		'jaialai',
		'cat_bingo',
		'cat_slotmachines',
		'cat_tablegames',
		'is_scrap',
		'address',
		'telephone',
		'website',
		'facebook',
		'twitter',
		'toll_free',
		'img_url',
        'has_original_img'
	];
}
