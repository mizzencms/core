<?php

namespace Strayobject\Mizzenlite\Page;

use Strayobject\Mizzenlite\Base;
use Strayobject\Mizzenlite\Page\PageRepository;
use Strayobject\Mizzenlite\Page\Page;
use Symfony\Component\Finder\Finder;
use CommonApi\Exception\InvalidArgumentException;
/**
 * @todo  change name to generator
 */

class PageRepositoryProvider extends Base
{
    private $pageRepository;
    private $finder;

    public function __construct(PageRepository $pageRepository, Finder $finder)
    {
        parent::__construct();

        $this->setPageRepository($pageRepository);
        $this->setFinder($finder);
    }
    /**
     * @todo remove coupling, redo 404
     * @todo limit finder to extension set in config
     * @todo package should not rely on module
     * @return [type] [description]
     */
    public function populateRepository()
    {
        $location = $this->getBag()->get('config')->pages->location;

        if (!file_exists($location)) {
            throw new InvalidArgumentException('Page location does not exist');
        }

        $extension  = $this->getBag()->get('config')->pages->extension;
        $finder     = $this->getFinder()->in($location);
        $metaParser = $this->getBag()->get('metaParser');

        foreach ($finder as $f) {
            $content = $f->getContents();
            $path    = str_replace($extension, '', $f->getRelativePathname());
            $p       = new Page();

            $p->setLocation((string) $f);
            $p->setPath($path);
            $p->setMeta($metaParser->parse($content));
            $p->setContent($metaParser->removeMetaString());
            $p->setIsDir($f->isDir());

            $this->getPageRepository()->add($p);
        }

        // add 404
        $notFound = new Page();
        $notFound->setLocation($location.'404'.$extension);
        $notFound->setPath('404');
        $notFound->setMeta(
            $metaParser->parse(file_get_contents($notFound->getLocation()))
        );
        $notFound->setContent($metaParser->removeMetaString());

        $this->getPageRepository()->add($notFound);

        return $this;
    }

    /**
     * Gets the value of pageRepository.
     *
     * @return mixed
     */
    public function getPageRepository()
    {
        return $this->pageRepository;
    }

    /**
     * Sets the value of pageRepository.
     *
     * @param mixed $pageRepository the page repository
     *
     * @return self
     */
    public function setPageRepository(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;

        return $this;
    }

    /**
     * Gets the value of finder.
     *
     * @return mixed
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * Sets the value of finder.
     *
     * @param mixed $finder the finder
     *
     * @return self
     */
    public function setFinder($finder)
    {
        $this->finder = $finder;

        return $this;
    }
}