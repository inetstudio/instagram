<?php

namespace InetStudio\Instagram\Stories\Pipelines\Filters;

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
     * @param mixed $story
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($story, Closure $next)
    {
        if (! ($story && ! in_array((string) $story->getUser()->getPk(), $this->usersPKs))) {
            $story = null;
        }

        return $next($story);
    }
}
