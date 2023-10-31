<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 * @property int $id
 * @property string $country_title
 * @property string $country_name
 * @property string|null $header_text
 * @property string|null $footer_text
 * @property bool $done
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCountryTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereFooterText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereHeaderText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
	protected $table = 'category';
	public $timestamps = false;

	protected $casts = [
		'done' => 'bool'
	];

	protected $fillable = [
		'country_title',
		'country_name',
		'header_text',
		'footer_text',
		'done'
	];
}
