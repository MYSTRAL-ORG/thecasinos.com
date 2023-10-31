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
 *
 * @package App\Models
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
