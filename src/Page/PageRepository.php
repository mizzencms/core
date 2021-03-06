<?php

namespace Mizzencms\Core\Page;

use Mizzencms\Core\Page\PageInterface;
use Mizzencms\Core\Base;

class PageRepository extends Base
{
    private $pages;

    public function add(PageInterface $page)
    {
        $this->pages[$page->getPath()] = $page;
    }

    public function delete($path)
    {
        if (isset($this->pages[$path])) {
            unset($this->pages[$path]);
        }
    }
    /**
     * @param  string $path
     * @return PageInterface
     */
    public function findByPath($path)
    {
        if (isset($this->pages[$path])) {
            return $this->pages[$path];
        } else {
            header('HTTP/1.0 404 Not Found', true, 404);
            return $this->pages['404'];
        }
    }

    public function findAllInDirPath($dirPath)
    {
        $ret = [];

        foreach ($this->pages as $page) {
            if (strpos($page->getPath(), $dirPath) !== false) {
                $ret[] = $page;
            }
        }

        return $ret;
    }

    public function findAll()
    {
        return $this->pages;
    }

    /**
     * Gets the value of pages.
     *
     * @return mixed
     */
    protected function getPages()
    {
        return $this->pages;
    }
    
    /**
     * Sets the value of pages.
     *
     * @param mixed $pages the pages 
     *
     * @return self
     */
    protected function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }
}
