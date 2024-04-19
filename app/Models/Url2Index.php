<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Url2Index
 *
 * @property int $id
 * @property string $url
 * @property bool $status
 *
 * @package App\Models
 */
class Url2Index extends Model
{
    protected $table = 'url_2_index';
    public $timestamps = false;

    protected $casts = [
        'status' => 'bool'
    ];
    protected $fillable = ['url', 'status'];
}
