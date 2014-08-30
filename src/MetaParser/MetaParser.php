<?php

namespace Strayobject\Mizzenlite\MetaParser;

use Strayobject\Mizzenlite\Base;
use Strayobject\Mizzenlite\Container;

class MetaParser extends Base
{
    private $content;

    public function parse($content)
    {
        $this->setContent($content);

        return $this->getMetaVars();
    }

    public function getMetaString()
    {
        preg_match('/----(.+?)----/s', $this->getContent(), $matches);

        return isset($matches[1]) ? $matches[1] : '';
    }

    public function removeMetaString()
    {
        return preg_replace('/----(.+?)----/s', '', $this->getContent());
    }

    protected function getMetaVars()
    {
        $metaVars = preg_split('/['.PHP_EOL.']+/', $this->getMetaString(), -1, PREG_SPLIT_NO_EMPTY);

        if ($metaVars) {
            $meta = new \StdClass();

            foreach ($metaVars as $var) {
                @list($name, $value) = preg_split('/[:]/', $var, 2, PREG_SPLIT_NO_EMPTY);

                $meta->$name = $value;
            }

            return $meta;
        }
    }

    /**
     * Gets the value of content.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the value of content.
     *
     * @param mixed $content the content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}