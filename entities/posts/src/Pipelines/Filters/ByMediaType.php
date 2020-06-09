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
        if ($post->getMediaType() === 8) {
            $passCheck = false;

            foreach ($post->getCarouselMedia() as $carouselMedia) {
                if (in_array($carouselMedia->getMediaType(), $this->mediaTypes)) {
                    $passCheck = true;

                    break;
                }
            }

            if (! $passCheck) {
                $post = null;
            }
        } else {
            if (! ($post && in_array($post->getMediaType(), $this->mediaTypes))) {
                $post = null;
            }
        }

        return $next($post);
    }
}
