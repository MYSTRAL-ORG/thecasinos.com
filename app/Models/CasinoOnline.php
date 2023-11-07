<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CasinoOnline
 *
 * @property int $id
 * @property string|null $nom_casino
 * @property string|null $key_feature
 * @property string|null $screenshot
 * @property string|null $logo
 * @property string|null $point_pour
 * @property string|null $point_contre
 * @property string|null $bonus
 * @property string|null $sumup_description
 * @property string|null $bonus_description
 * @property string|null $deposit_mehods
 * @property string|null $contact_information
 * @property string|null $register_link
 * @property string|null $note
 * @property string|null $sous_titre
 * @property string|null $description
 * @property string|null $deposit_mehods_description
 * @property string|null $contact_information_description
 * @property string|null $icone
 * @property bool|null $actif
 * @property string|null $nom_casino_slug
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline query()
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereBonusDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereContactInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereContactInformationDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereDepositMehods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereDepositMehodsDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereIcone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereKeyFeature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereNomCasino($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereNomCasinoSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline wherePointContre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline wherePointPour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereRegisterLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereScreenshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereSousTitre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CasinoOnline whereSumupDescription($value)
 * @mixin \Eloquent
 */
class CasinoOnline extends Model
{
	protected $table = 'casino_online';
	public $timestamps = false;

	protected $casts = [
		'actif' => 'bool'
	];
//fillable
	protected $fillable = [
		'nom_casino',
		'key_feature',
		'screenshot',
		'logo',
		'point_pour',
		'point_contre',
		'bonus',
		'sumup_description',
		'bonus_description',
		'deposit_mehods',
		'contact_information',
		'register_link',
		'note',
		'sous_titre',
		'description',
		'deposit_mehods_description',
		'contact_information_description',
		'icone',
		'actif',
		'nom_casino_slug'
	];
}
