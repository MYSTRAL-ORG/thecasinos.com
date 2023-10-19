<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CasinoDetailsSource
 *
 * @property int $id
 * @property int $id_casino
 * @property string|null $source_openai
 * @property bool $is_done
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetailsSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetailsSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetailsSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetailsSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetailsSource whereIdCasino($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetailsSource whereIsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetailsSource whereSourceOpenai($value)
 * @mixin \Eloquent
 */
class CasinoDetailsSource extends Model
{
	protected $table = 'casino_details_source';
	protected $primaryKey = 'id_casino';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'id_casino' => 'int',
		'is_done' => 'bool'
	];

	protected $fillable = [
		'id',
		'source_openai',
		'is_done'
	];
}
