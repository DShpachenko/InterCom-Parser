<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pages.
 *
 * @package App\Models
 * @property int $id
 * @property int uid
 * @property int $created_at
 * @property int $updated_at
 */
class Pages extends Model
{
    /**
     * @var string
     */
    protected $table = 'pages';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'uid',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function components(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\PageComponents::class, 'page_id');
    }
}
