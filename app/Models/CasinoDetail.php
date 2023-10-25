<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

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
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $seo_keywords
 * @property bool|null $done
 * 
 * @property Casino $casino
 *
 * @package App\Models
 */
class CasinoDetail extends Model
{
	protected $table = 'casino_details';
	public $timestamps = false;

	protected $casts = [
		'id_casino' => 'int',
		'done' => 'bool'
	];

	protected $fillable = [
		'title',
		'description',
		'sumup',
		'games',
		'fun_facts',
		'resume_1_line',
		'resume_2_words',
		'id_casino',
		'seo_title',
		'seo_description',
		'seo_keywords',
		'done'
	];

	public function casino()
	{
		return $this->belongsTo(Casino::class, 'id_casino');
	}
}
