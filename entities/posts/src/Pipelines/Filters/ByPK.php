<?php

namespace InetStudio\Instagram\Posts\Pipelines\Filters;

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
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if (! $post) {
            return $next($post);
        }

        if (in_array((string) $post->getPk(), $this->PKs)) {
            $post = null;
        }

        return $next($post);
    }
}
