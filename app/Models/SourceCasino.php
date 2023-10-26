<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SourceCasino
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property array|null $data
 * @property bool|null $done
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|SourceCasino newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceCasino newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceCasino query()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceCasino whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceCasino whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceCasino whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceCasino whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceCasino whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SourceCasino extends Model
{
	protected $table = 'source_casinos';

	protected $casts = [
		'data' => 'json',
		'done' => 'bool'
	];

	protected $fillable = [
		'data',
		'done'
	];
}
