<?php

namespace InetStudio\Instagram\Comments\Services\Back;

use InstagramAPI\Response\Model\Comment;
use InetStudio\AdminPanel\Services\Back\BaseService;
use InetStudio\Instagram\Comments\Contracts\Models\CommentModelContract;
use InetStudio\Instagram\Comments\Contracts\Services\Back\CommentsServiceContract;

/**
 * Class CommentsService.
 */
class CommentsService extends BaseService implements CommentsServiceContract
{
    /**
     * CommentsService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Instagram\Comments\Contracts\Repositories\CommentsRepositoryContract'));
    }

    /**
     * Сохраняем модель.
     *
     * @param Comment $comment
     *
     * @return CommentModelContract
     */
    public function save(Comment $comment): CommentModelContract
    {
        $data = [
            'pk' => $comment->getPk(),
            'post_pk' => $comment->getMediaId(),
            'user_pk' => $comment->getUser()->getPk(),
            'additional_info' => json_decode(json_encode($comment), true),
        ];

        $item = $this->repository->saveInstagramObject($data, $comment->getPk());

        return $item;
    }
}
