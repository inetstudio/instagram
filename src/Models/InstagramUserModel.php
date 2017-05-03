<?php

namespace InetStudio\Instagram\Models;

use Emojione\Emojione as Emoji;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

/**
 * Модель пользователя в инстаграме.
 *
 * Class InstagramUser
 */
class InstagramUserModel extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

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
        'pk', 'username', 'full_name', 'profile_pic_url', 'follower_count', 'following_count', 'media_count',
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
     * Загрузка модели
     * Событие удаления пользователя инстаграм.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->posts()->delete();
            $user->comments()->delete();
        });
    }

    /**
     * Отношение "один ко многим" с моделью поста в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(InstagramPostModel::class, 'user_pk', 'pk');
    }

    /**
     * Отношение "один ко многим" с моделью комментария в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(InstagramCommentModel::class, 'user_pk', 'pk');
    }

    /**
     * Получаем никнейм пользователя инстаграм.
     *
     * @return string
     */
    public function getUserNicknameAttribute()
    {
        return '@'.trim($this->username, '@');
    }

    /**
     * Получаем ссылку на профиль пользователя инстаграм.
     *
     * @return string
     */
    public function getUserURLAttribute()
    {
        return 'https://instagram.com/'.trim($this->username, '@');
    }

    /**
     * Получаем имя пользователя.
     *
     * @return mixed
     */
    public function getUserFullNameAttribute()
    {
        return ($this->full_name) ? Emoji::shortnameToUnicode($this->full_name) : $this->userNickname;
    }
}
