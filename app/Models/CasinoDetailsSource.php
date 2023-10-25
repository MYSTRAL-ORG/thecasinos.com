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
 * @property bool $is_done
 * @property array|null $source_openai_json
 * @property string|null $source_openai
 *
 * @package App\Models
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
		'is_done' => 'bool',
		'source_openai_json' => 'json',
		'source_openai' => 'binary'
	];

	protected $fillable = [
		'id',
        'id_casino',
		'is_done',
		'source_openai_json',
		'source_openai'
	];
}
