<?php

namespace InetStudio\Instagram\Users\Services\Back;

use InstagramAPI\Response\Model\User;
use InetStudio\AdminPanel\Services\Back\BaseService;
use InetStudio\Instagram\Users\Contracts\Models\UserModelContract;
use InetStudio\Instagram\Users\Contracts\Services\Back\UsersServiceContract;

/**
 * Class UsersService.
 */
class UsersService extends BaseService implements UsersServiceContract
{
    /**
     * UsersService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Instagram\Users\Contracts\Repositories\UsersRepositoryContract'));
    }

    /**
     * Сохраняем модель.
     *
     * @param User $user
     *
     * @return UserModelContract
     */
    public function save(User $user): UserModelContract
    {
        $data = [
            'pk' => $user->getPk(),
            'additional_info' => json_decode(json_encode($user), true),
        ];

        $item = $this->repository->saveInstagramObject($data, $user->getPk());
        $this->attachMedia($item, $user);

        return $item;
    }

    /**
     * Аттачим медиа к модели.
     *
     * @param UserModelContract $item
     * @param User $user
     */
    protected function attachMedia(UserModelContract $item, User $user): void
    {
        $name = $user->getProfilePicId() ?? 'empty';
        $currentMedia = $item->getMedia('media')->pluck('name')->toArray();

        if ($user->getProfilePicUrl() && ! in_array($name, $currentMedia)) {
            $item->addMediaFromUrl($user->getProfilePicUrl())
                ->usingName($name)
                ->toMediaCollection('media', 'instagram_users');
        }
    }
}
