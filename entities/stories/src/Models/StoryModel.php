<?php

namespace InetStudio\Instagram\Stories\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Uploads\Models\Traits\HasImages;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\Instagram\Stories\Contracts\Models\StoryModelContract;

/**
 * Class StoryModel.
 */
class StoryModel extends Model implements StoryModelContract, HasMedia
{
    use HasImages;
    use SoftDeletes;
    use HasJSONColumns;

    const ENTITY_TYPE = 'instagram_story';

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
        'config' => 'instagram_stories',
        'model' => 'story',
    ];

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'instagram_stories';

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['media_type', 'social_name'];

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
     * Геттер атрибута media_type.
     *
     * @return string
     */
    public function getMediaTypeAttribute(): string
    {
        switch ($this->additional_info['media_type']) {
            case 1:
                return 'story_photo';
            case 8:
                return 'story_carousel';
            case 2:
                return 'story_video';
        }

        return '';
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Геттер атрибута social_name.
     *
     * @return string
     */
    public function getSocialNameAttribute(): string
    {
        return self::NETWORK;
    }

    /**
     * Обратное отношение "один ко многим" с моделью пользователя в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @throws BindingResolutionException
     */
    public function user()
    {
        return $this->belongsTo(
            app()->make('InetStudio\Instagram\Users\Contracts\Models\UserModelContract'),
            'user_pk',
            'pk'
        );
    }
}
