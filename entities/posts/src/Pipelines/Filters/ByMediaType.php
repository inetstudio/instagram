<?php

namespace InetStudio\Instagram\Posts\Pipelines\Filters;

use Closure;

/**
 * Class ByMediaType.
 */
class ByMediaType
{
    /**
     * @var array
     */
    protected $mediaTypes;

    /**
     * ByMediaType constructor.
     *
     * @param array $mediaTypes
     */
    public function __construct(array $mediaTypes = [])
    {
       $this->mediaTypes = $mediaTypes;
    }

    /**
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if (! ($post && in_array($post->getMediaType(), $this->mediaTypes))) {
            $post = null;
        }

        return $next($post);
    }
}
