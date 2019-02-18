<?php

namespace InetStudio\Instagram\Comments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use InetStudio\Instagram\Comments\Contracts\Models\CommentModelContract;

/**
 * Class CommentModel.
 */
class CommentModel extends Model implements CommentModelContract
{
    use SoftDeletes;
    use HasJSONColumns;

    const ENTITY_TYPE = 'instagram_comment';

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
        'pk', 'post_pk', 'user_pk', 'additional_info',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы к базовым типам.
     *
     * @var array
     */
    protected $casts = [
        'additional_info' => 'array',
    ];

    /**
     * Сеттер атрибута pk.
     *
     * @param $value
     */
    public function setPkAttribute($value)
    {
        $this->attributes['pk'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута post_pk.
     *
     * @param $value
     */
    public function setPostPkAttribute($value)
    {
        $this->attributes['post_pk'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута user_pk.
     *
     * @param $value
     */
    public function setUserPkAttribute($value)
    {
        $this->attributes['user_pk'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута additional_info.
     *
     * @param $value
     */
    public function setAdditionalInfoAttribute($value)
    {
        $this->attributes['additional_info'] = json_encode((array) $value);
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Обратное отношение "один ко многим" с моделью пользователя в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            app()->make('InetStudio\Instagram\Users\Contracts\Models\UserModelContract'),
            'user_pk',
            'pk'
        );
    }

    /**
     * Обратное отношение "один ко многим" с моделью поста в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(
            app()->make('InetStudio\Instagram\Posts\Contracts\Models\PostModelContract'),
            'post_pk',
            'pk'
        );
    }
}
