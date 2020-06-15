<?php

namespace InetStudio\Instagram\Stories\Pipelines\Filters;

use Closure;

/**
 * Class ByPK.
 */
class ByPK
{
    /**
     * @var array
     */
    protected $PKs;

    /**
     * ByMediaType constructor.
     *
     * @param array $PKs
     */
    public function __construct(array $PKs = [])
    {
       $this->PKs = $PKs;
    }

    /**
     * @param mixed $story
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($story, Closure $next)
    {
        if (! $story) {
            return $next($story);
        }

        if (in_array((string) $story->getPk(), $this->PKs)) {
            $story = null;
        }

        return $next($story);
    }
}
