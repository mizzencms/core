<?php

namespace Strayobject\Mizzenlite\Page;

interface PageInterface
{
    public function isDir();
    public function getMeta();
    public function setMeta($meta);
    public function getContent();
    public function setContent($content);
    public function getPath();
    public function setPath($path);
    public function getLocation();
    public function setLocation($location);
    public function getIsDir();
    public function setIsDir($isDir);
}