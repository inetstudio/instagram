<?php

namespace InetStudio\Instagram\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель комментария в инстаграме
 *
 * Class InstagramComment
 * @package InetStudio\Instagram\Models
 */
class InstagramCommentModel extends Model
{
    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'instagram_comments';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'pk', 'post_pk', 'user_pk', 'text', 'created_at_utc'
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at_utc',
        'created_at',
        'updated_at',
    ];

    /**
     * Обратное отношение "один ко многим" с моделью пользователя в инстаграме
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->belongsTo(InstagramUserModel::class, 'user_pk', 'pk');
    }

    /**
     * Обратное отношение "один ко многим" с моделью поста в инстаграме
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function post()
    {
        return $this->belongsTo(InstagramPostModel::class, 'post_pk', 'pk');
    }
}
