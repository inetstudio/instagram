<?php

namespace InetStudio\Instagram\Stories\Pipelines\Filters;

use Closure;

/**
 * Class ByCreatedLt.
 */
class ByCreatedLt
{
    /**
     * @var
     */
    protected $endTime;

    /**
     * ByCreatedLt constructor.
     *
     * @param mixed $endTime
     */
    public function __construct($endTime)
    {
       $this->endTime = $endTime;
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

        if (! ($this->endTime && $story->getTakenAt() < $this->endTime)) {
            $story = null;
        }

        return $next($story);
    }
}
