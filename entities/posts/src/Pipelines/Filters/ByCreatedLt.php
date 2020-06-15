<?php

namespace InetStudio\Instagram\Posts\Pipelines\Filters;

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

        if (! ($this->endTime && $post->getTakenAt() < $this->endTime)) {
            $post = null;
        }

        return $next($post);
    }
}
