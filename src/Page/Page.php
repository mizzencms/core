<?php

namespace Strayobject\Mizzenlite\Page;

use Strayobject\Mizzenlite\Page\PageInterface;

class Page implements PageInterface
{
    private $meta;
    private $content;
    private $path;
    private $location;
    private $isDir;

    public function isDir()
    {
        return $this->getIsDir();
    }
    /**
     * Gets the value of meta.
     *
     * @return mixed
     */
    public function getMeta()
    {
        return $this->meta;
    }
    
    /**
     * Sets the value of meta.
     *
     * @param mixed $meta the meta 
     *
     * @return self
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
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

    /**
     * Gets the value of path.
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Sets the value of path.
     *
     * @param mixed $path the path 
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Gets the value of location.
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }
    
    /**
     * Sets the value of location.
     *
     * @param mixed $location the location 
     *
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Gets the value of isDir.
     *
     * @return mixed
     */
    public function getIsDir()
    {
        return $this->isDir;
    }
    
    /**
     * Sets the value of isDir.
     *
     * @param mixed $isDir the is dir 
     *
     * @return self
     */
    public function setIsDir($isDir)
    {
        $this->isDir = $isDir;

        return $this;
    }
}