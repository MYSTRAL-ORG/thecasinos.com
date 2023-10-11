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
 * @property string $data
 *
 * @package App\Models
 */
class SourceCasino extends Model
{
	protected $table = 'source_casinos';

	protected $casts = [
		'data' => 'binary'
	];

	protected $fillable = [
		'data'
	];
}
