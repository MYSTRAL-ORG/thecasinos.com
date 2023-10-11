<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpatialRefSy
 * 
 * @property int $srid
 * @property string|null $auth_name
 * @property int|null $auth_srid
 * @property string|null $srtext
 * @property string|null $proj4text
 *
 * @package App\Models
 */
class SpatialRefSy extends Model
{
	protected $table = 'spatial_ref_sys';
	protected $primaryKey = 'srid';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'srid' => 'int',
		'auth_srid' => 'int'
	];

	protected $fillable = [
		'auth_name',
		'auth_srid',
		'srtext',
		'proj4text'
	];
}
