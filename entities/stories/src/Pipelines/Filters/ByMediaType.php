<?php

namespace InetStudio\Instagram\Stories\Pipelines\Filters;

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
     * @param mixed $story
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($story, Closure $next)
    {
        if (! ($story && in_array($story->getMediaType(), $this->mediaTypes))) {
            $story = null;
        }

        return $next($story);
    }
}
