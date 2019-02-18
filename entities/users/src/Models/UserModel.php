<?php

namespace InetStudio\Instagram\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Uploads\Models\Traits\HasImages;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use InetStudio\Instagram\Users\Contracts\Models\UserModelContract;

/**
 * Class UserModel.
 */
class UserModel extends Model implements UserModelContract, HasMedia
{
    use HasImages;
    use SoftDeletes;
    use HasJSONColumns;

    const ENTITY_TYPE = 'instagram_user';

    /**
     * Конфиг изображений.
     *
     * @var array
     */
    protected $images = [
        'config' => 'instagram_users',
        'model' => 'user',
    ];

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'instagram_users';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'pk', 'additional_info',
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
     * Сеттер атрибута additional_info.
     *
     * @param $value
     */
    public function setAdditionalInfoAttribute($value)
    {
        $this->attributes['additional_info'] = json_encode((array) $value);
    }

    /**
     * Геттер атрибута nickname.
     *
     * @return string
     */
    public function getNicknameAttribute()
    {
        return '@'.$this->additional_info['username'];
    }

    /**
     * Геттер атрибута url.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return 'https://instagram.com/'.$this->additional_info['username'];
    }

    /**
     * Геттер атрибута full_name.
     *
     * @return mixed
     */
    public function getFullNameAttribute()
    {
        return $this->additional_info['full_name'] ?? $this->additional_info['username'];
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
     * Отношение "один ко многим" с моделью поста в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(
            app()->make('InetStudio\Instagram\Posts\Contracts\Models\PostModelContract'),
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
            'user_pk',
            'pk'
        );
    }
}
