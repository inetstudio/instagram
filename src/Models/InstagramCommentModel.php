<?php

namespace InetStudio\Instagram\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель комментария в инстаграме.
 * 
 * Class InstagramComment
 *
 * @property int $id
 * @property string $pk
 * @property string $post_pk
 * @property string $user_pk
 * @property string $text
 * @property \Carbon\Carbon $created_at_utc
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \InetStudio\Instagram\Models\InstagramPostModel $post
 * @property-read \InetStudio\Instagram\Models\InstagramUserModel $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Instagram\Models\InstagramCommentModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel whereCreatedAtUtc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel wherePk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel wherePostPk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Instagram\Models\InstagramCommentModel whereUserPk($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Instagram\Models\InstagramCommentModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Instagram\Models\InstagramCommentModel withoutTrashed()
 * @mixin \Eloquent
 */
class InstagramCommentModel extends Model
{
    use SoftDeletes;

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
        'pk', 'post_pk', 'user_pk', 'text', 'created_at_utc',
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
        'deleted_at',
    ];

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
     * Обратное отношение "один ко многим" с моделью поста в инстаграме.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function post()
    {
        return $this->belongsTo(InstagramPostModel::class, 'post_pk', 'pk');
    }
}
