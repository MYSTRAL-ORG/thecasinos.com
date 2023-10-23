<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CasinoDetail
 *
 * @property string|null $title
 * @property string|null $description
 * @property string|null $sumup
 * @property string|null $games
 * @property string|null $fun_facts
 * @property string|null $resume_1_line
 * @property string|null $resume_2_words
 * @property int $id
 * @property int $id_casino
 * @property Casino $casino
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereFunFacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereIdCasino($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereResume1Line($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereResume2Words($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereSumup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoDetail whereTitle($value)
 * @mixin \Eloquent
 */
class CasinoDetail extends Model
{
    use HasFactory;
	protected $table = 'casino_details';
	public $timestamps = false;

	protected $casts = [
		'id_casino' => 'int'
	];

	protected $fillable = [
		'title',
		'description',
		'sumup',
		'games',
		'fun_facts',
		'resume_1_line',
		'resume_2_words',
		'id_casino'
	];

	public function casino()
	{
		return $this->belongsTo(Casino::class, 'id_casino');
	}
}
