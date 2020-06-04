<?php

namespace InetStudio\Instagram\Stories\Pipelines\Filters;

use Closure;

/**
 * Class ByCreatedGt.
 */
class ByCreatedGt
{
    /**
     * @var
     */
    public $startTime;

    /**
     * ByCreatedGt constructor.
     *
     * @param mixed $startTime
     */
    public function __construct($startTime)
    {
       $this->startTime = $startTime;
    }

    /**
     * @param mixed $story
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($story, Closure $next)
    {
        if (! ($story && $this->startTime && $story->getTakenAt() > $this->startTime)) {
            $story = null;
        }

        return $next($story);
    }
}
