<?php

namespace InetStudio\Instagram\Models;

use Spatie\Image\Manipulations;
use Emojione\Emojione as Emoji;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * Модель поста в инстаграме.
 *
 * Class InstagramPost
 */
class InstagramPostModel extends Model implements HasMediaConversions
{
    use SoftDeletes;
    use HasMediaTrait;

    /**
     * Имя социальной сети.
     */
    const NETWORK = 'instagram';

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
        'pk', 'user_pk', 'media_type', 'image_versions', 'video_versions',
        'code', 'view_count', 'comment_count', 'like_count', 'caption',
        'taken_at',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'taken_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Загрузка модели
     * Событие удаления поста в инстаграме.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
            $post->comments()->delete();
        });
    }

    /**
     * Обратное отношение "один ко многим" с моделью пользователя в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->belongsTo(InstagramUserModel::class, 'user_pk', 'pk');
    }

    /**
     * Отношение "один ко многим" с моделью комментария в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(InstagramCommentModel::class, 'post_pk', 'pk');
    }

    /**
     * Получаем ссылку на пост в инстаграме.
     *
     * @return string
     */
    public function getPostURLAttribute()
    {
        return 'https://instagram.com/p/'.$this->code;
    }

    /**
     * Получаем текст поста.
     *
     * @param $value
     * @return mixed
     */
    public function getCaptionAttribute($value)
    {
        return Emoji::shortnameToUnicode($value);
    }

    /**
     * Создаем превью при сохранении изображений.
     */
    public function registerMediaConversions()
    {
        $this->addMediaConversion('edit_thumb')
            ->fit(Manipulations::FIT_CONTAIN, 96, 96)
            ->performOnCollections('images');

        $this->addMediaConversion('index_thumb')
            ->fit(Manipulations::FIT_CONTAIN, 320, 320)
            ->performOnCollections('images');
    }
}
