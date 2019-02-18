<?php

namespace InetStudio\Instagram\Posts\Pipelines\Filters;

use Closure;
use Emojione\Emojione as Emoji;

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
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if ($post && is_array($this->tag) && count($this->tag) > 1) {
            $caption = $post->getCaption()->getText();

            $caption = ($caption) ? Emoji::toShort($caption) : '';
            $caption = preg_replace('/:pound_symbol:/', '#', $caption);

            preg_match_all('/(#[а-яА-Яa-zA-Z0-9]+)/u', $caption, $postTags);

            $postTags = array_map(function ($value) {
                return mb_strtolower($value);
            }, $postTags[0]);

            if (count(array_intersect($this->tag, $postTags)) != count($this->tag)) {
                $post = null;
            }
        }

        return $next($post);
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
                return '#'.trim($value, '#');
            }, $tag);
        } else {
            return '#'.trim($tag, '#');
        }
    }
}
