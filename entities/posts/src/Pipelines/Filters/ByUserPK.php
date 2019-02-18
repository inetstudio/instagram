<?php

namespace InetStudio\Instagram\Posts\Pipelines\Filters;

use Closure;

/**
 * Class ByUserPK.
 */
class ByUserPK
{
    /**
     * @var array
     */
    protected $usersPKs;

    /**
     * ByMediaType constructor.
     *
     * @param array $usersPKs
     */
    public function __construct(array $usersPKs = [])
    {
       $this->usersPKs = $usersPKs;
    }

    /**
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if (! ($post && ! in_array((string) $post->getUser()->getPk(), $this->usersPKs))) {
            $post = null;
        }

        return $next($post);
    }
}
