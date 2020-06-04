<?php

namespace InetStudio\Instagram\Stories\Pipelines\Filters;

use Closure;

/**
 * Class ByTags.
 */
class ByTags
{
    /**
     * @var array
     */
    protected $tag;

    /**
     * ByTags constructor.
     *
     * @param mixed $tag
     */
    public function __construct($tag)
    {
        $this->tag = $this->prepareTag($tag);
    }

    /**
     * @param mixed $item
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($item, Closure $next)
    {
        if ($item) {
            $storyHashtags = $item->getStoryHashtags();

            $tags = [];
            foreach ($storyHashtags as $storyHashtag) {
                $hashtag = $storyHashtag->getHashtag();

                $tags[] = $hashtag->getName();
            }

            $tags = array_map(function ($value) {
                return '#'.trim(mb_strtolower($value), '#');
            }, $tags);

            if (count(array_intersect($this->tag, $tags)) != count($this->tag)) {
                $item = null;
            }
        }

        return $next($item);
    }

    /**
     * Приводим полученные теги к нужному виду.
     *
     * @param $tag
     *
     * @return array|string
     */
    protected function prepareTag($tag)
    {
        if (is_array($tag)) {
            return array_map(function ($value) {
                return '#'.trim(mb_strtolower($value), '#');
            }, $tag);
        } else {
            return '#'.trim(mb_strtolower($tag), '#');
        }
    }
}
