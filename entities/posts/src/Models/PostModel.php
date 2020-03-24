<?php

namespace InetStudio\Instagram\Posts\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Uploads\Models\Traits\HasImages;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use InetStudio\Instagram\Posts\Contracts\Models\PostModelContract;

/**
 * Class PostModel.
 */
class PostModel extends Model implements PostModelContract, HasMedia
{
    use HasImages;
    use SoftDeletes;
    use HasJSONColumns;

    const ENTITY_TYPE = 'instagram_post';

    /**
     * Имя социальной сети.
     */
    const NETWORK = 'instagram';

    /**
     * Конфиг изображений.
     *
     * @var array
     */
    protected $images = [
        'config' => 'instagram_posts',
        'model' => 'post',
    ];

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'instagram_posts';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'pk', 'user_pk', 'additional_info',
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
     * Геттер атрибута caption.
     *
     * @return mixed
     */
    public function getCaptionAttribute()
    {
        return $this->additional_info['caption']['text'];
    }

    /**
     * Геттер атрибута url.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return 'https://instagram.com/p/'.$this->additional_info['code'];
    }

    /**
     * Геттер атрибута media_type.
     *
     * @return string
     */
    public function getMediaTypeAttribute()
    {
        switch ($this->additional_info['media_type']) {
            case 1:
                return 'photo';
            case 8:
                return 'carousel';
            case 2:
                return 'video';
        }
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
     * Геттер атрибута social_name.
     *
     * @return string
     */
    public function getSocialNameAttribute()
    {
        return self::NETWORK;
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
     * Отношение "один ко многим" с моделью комментария в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(
            app()->make('InetStudio\Instagram\Comments\Contracts\Models\CommentModelContract'),
            'post_pk',
            'pk'
        );
    }
}
