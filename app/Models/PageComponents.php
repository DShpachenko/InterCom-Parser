<?php

namespace App\Models;

use App\Observers\PageComponentsObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PageComponents.
 *
 * @package App\Models
 * @property int $id
 * @property int $page_id
 * @property string $key
 * @property string $value
 * @property int $created_at
 * @property int $updated_at
 */
class PageComponents extends Model
{
    /**
     * Черный текст.
     */
    public const TYPE_BLACK_TEXT = 0;

    /**
     * Черный жирный текст.
     */
    public const TYPE_BOLD_BLACK_TEXT = 1;

    /**
     * Оранжевый текст.
     */
    public const TYPE_ORANGE_TEXT = 2;

    /**
     * Ссылка.
     */
    public const TYPE_LINK = 3;

    /**
     * Код.
     */
    public const TYPE_CODE = 4;

    /**
     * Заголовок.
     */
    public const TYPE_HEADER = 5;

    /**
     * Список.
     */
    public const TYPE_LIST = 6;

    /**
     * @var string
     */
    protected $table = 'page_components';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'page_id',
        'key',
        'value',
    ];

    /**
     * Подключениие Observer для модели пользователя.
     */
    public static function boot(): void
    {
        parent::boot();
        static::observe(new PageComponentsObserver);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Pages::class);
    }
}
