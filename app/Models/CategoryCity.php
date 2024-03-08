<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CategoryCity
 *
 * @property int $id
 * @property string $city_title
 * @property string $city_name
 * @property string|null $header_text
 * @property string|null $footer_text
 * @property bool $done
 * @property string|null $country_name
 * @property string|null $country_title
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity whereCityTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity whereCountryTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity whereFooterText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity whereHeaderText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryCity whereId($value)
 * @mixin \Eloquent
 */
class CategoryCity extends Model
{
	protected $table = 'category_city';
	public $timestamps = false;

	protected $casts = [
		'done' => 'bool'
	];

	protected $fillable = [
		'city_title',
		'city_name',
		'header_text',
		'footer_text',
		'done',
		'country_name',
		'country_title'
	];
}
