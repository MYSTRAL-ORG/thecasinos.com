<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
 * @property string|null $owners
 * @property bool|null $always_open
 * @property string|null $poker_room_name
 * @property int|null $table_games
 * @property string|null $hotel_name
 * @property bool|null $valet
 * @property bool|null $self_parking
 * @property Collection|CasinoDetail[] $casino_details
 * @package App\Models
 * @property-read int|null $casino_details_count
 * @method static \Illuminate\Database\Eloquent\Builder|Casino newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Casino newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Casino query()
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereAlwaysOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatBingo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatCasino($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatGreyhounds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatHorseracing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatOfftrack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatPoker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatSimulcasting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatSlotmachines($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatSportsbook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCatTablegames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCityTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCountryIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereCountryTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereGamingMachines($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereHasOriginalImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereHotelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereHotels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereIdSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereIsScrap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereJaialai($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereLocationLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereLocationLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereOpened($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereOwners($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino wherePokerRoomName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino wherePokerTables($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereRestaurants($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereSelfParking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereShops($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereSpas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereSquareFootage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereStateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereStateTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereTableGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereTollFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereValet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereVenues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Casino whereWebsite($value)
 * @mixin \Eloquent
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
		'has_original_img' => 'bool',
		'always_open' => 'bool',
		'table_games' => 'int',
		'valet' => 'bool',
		'self_parking' => 'bool'
	];

	protected $fillable = [
		'',
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
		'has_original_img',
		'owners',
		'always_open',
		'poker_room_name',
		'table_games',
		'hotel_name',
		'valet',
		'self_parking',
        'link_interne_id'
	];

	public function casino_details()
	{
		return $this->hasMany(CasinoDetail::class, 'id_casino');
	}

}
