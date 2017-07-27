<?php

namespace InetStudio\Instagram\Models;

use Emojione\Emojione as Emoji;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * Модель поста в инстаграме.
 *
 * @property int $id
 * @property string $pk
 * @property string $user_pk
 * @property int $media_type
 * @property string $image_versions
 * @property string $video_versions
 * @property string $code
 * @property int $view_count
 * @property int $comment_count
 * @property int $like_count
 * @property mixed $caption
 * @property \Carbon\Carbon|null $taken_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\Instagram\Models\InstagramCommentModel[] $comments
 * @property-read string $post_url
 * @property-read string $social_name
 * @property-read string $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @property-read \InetStudio\Instagram\Models\InstagramUserModel $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Instagram\Models\InstagramPostModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereImageVersions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel wherePk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereTakenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereUserPk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereVideoVersions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramPostModel whereViewCount($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Instagram\Models\InstagramPostModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Instagram\Models\InstagramPostModel withoutTrashed()
 * @mixin \Eloquent
 */
class InstagramPostModel extends Model implements HasMediaConversions
{
    use SoftDeletes;
    use HasMediaTrait;
    use SearchableTrait;

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
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'instagram_posts.code' => 10,
            'instagram_posts.caption' => 9,
            'user.username' => 8,
            'user.full_name' => 7,
        ],
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * Получаем тип поста в инстаграме.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        switch ($this->media_type) {
            case 1:
                return 'photo';
            case 2:
                return 'video';
        }
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
     * Получаем имя социальной сети.
     *
     * @return string
     */
    public function getSocialNameAttribute()
    {
        return $this::NETWORK;
    }

    /**
     * Создаем превью при сохранении изображений.
     */
    public function registerMediaConversions()
    {
        $quality = (config('instagram.images.quality')) ? config('instagram.images.quality') : 75;

        if (config('instagram.images.sizes.post')) {
            foreach (config('instagram.images.sizes.post') as $name => $size) {
                $this->addMediaConversion($name.'_thumb')
                    ->crop('crop-center', $size['width'], $size['height'])
                    ->quality($quality)
                    ->performOnCollections('images');
            }
        }
    }
}
