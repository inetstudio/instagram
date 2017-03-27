<?php

namespace InetStudio\Instagram\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель поста в инстаграме
 *
 * Class InstagramPost
 * @package InetStudio\Instagram\Models
 */
class InstagramPostModel extends Model
{
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
        'taken_at', 'device_timestamp'
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'taken_at',
        'device_timestamp',
        'created_at',
        'updated_at',
    ];

    /**
     * Загрузка модели
     * Событие удаления поста в инстаграме
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($post)
        {
            $post->comments()->delete();
            $post->images()->delete();
        });
    }

    use \InetStudio\UploadImage\Traits\Imagable;
    use \InetStudio\UploadVideo\Traits\Videoable;

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
     * Отношение "один ко многим" с моделью комментария в инстаграме
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(InstagramCommentModel::class, 'post_pk', 'pk');
    }

    /**
     * Получаем ссылку на пост в инстаграме
     *
     * @return string
     */
    public function getPostURLAttribute()
    {
        return 'https://instagram.com/p/' . $this->code;
    }
}
