<?php

namespace InetStudio\Instagram\Posts\Pipelines\Filters;

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
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if (! ($post && $this->startTime && $post->getTakenAt() > $this->startTime)) {
            $post = null;
        }

        return $next($post);
    }
}
