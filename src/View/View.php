<?php

namespace Plainmotif\Mizzenlite\view;

use Plainmotif\Mizzenlite\Base;

class View extends Base
{
    private $items;
    private $page;

    public function __construct()
    {
        parent::__construct();

        $this->items = [];
    }
    /**
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    public function render()
    {
        $this->triggerEvent('beforeViewRender', array('view' => $this));

        $source = $this->prepare()->loadTemplate();

        $this->triggerEvent('afterViewRender', array(
            'view'   => $this,
            'source' => &$source
        ));

        return $source;
    }

    public function renderContent($uri)
    {
        $this->triggerEvent('beforeViewRenderContent', array(
            'view'     => $this,
            'uri'      => &$uri,
        ));

        $view = clone $this;
        $view->setPage(
            $this->getBag()->getShared('pageRepository')->findByPath($uri)
        );
        $view->parse();

        $this->triggerEvent('afterViewRenderContent', array('view' => $view));

        return $view->getPage()->getContent();
    }
    /**
     * @todo having a clone flag here may be suboptimal
     * @param  [type]  $uri      [description]
     * @param  [type]  $template [description]
     * @param  boolean $clone    [description]
     * @return [type]            [description]
     */
    public function partial($uri, $template, $clone = true)
    {
        $this->triggerEvent('beforeViewPartial', array(
            'view'     => $this,
            'uri'      => &$uri,
            'template' => &$template
        ));
        if ($clone) {
            $view = clone $this;
        } else {
            $view = $this;
        }
        /**
         * @todo  remove when navigation is sorted out
         */
        $view->setBag($this->getBag());

        $view->setPage(
            $this->getBag()->getShared('pageRepository')->findByPath($uri)
        );

        $source = $view->prepare()->loadTemplate($template);

        $this->triggerEvent('afterViewPartial', array(
            'view'   => $view,
            'source' => &$source
        ));

        return $source;
    }

    protected function loadTemplate($templateLocation = null)
    {
        $skin = $this->getBag()->get('config')->skin;

        $templateLocation or $templateLocation = $skin->layout;

        $basePath = $this->getBag()->get('basePath')->path;
        $path     = $basePath.'/skin/'.$skin->name.'/'.$templateLocation;

        ob_start();
        include $path;

        return ob_get_clean();
    }

    protected function prepare()
    {
        $this->triggerEvent('beforeViewPrepare', array('view' => $this));
        /**
         * @todo remove navigation from here it should be a module
         */
        $this->navigation = $this->getBag()->getShared('navigation');

        $this->parse();
        $this->content    = $this->getPage()->getContent();
        $this->meta       = $this->getPage()->getMeta();

        $this->triggerEvent('afterViewPrepare', array('view' => $this));

        return $this;
    }

    protected function parse()
    {
        $this->triggerEvent(
            'beforeViewParseContent',
            array('view' => $this, 'page' => $this->getPage())
        );

        $this->triggerEvent(
            'viewParseContent',
            array('view' => $this, 'page' => $this->getPage())
        );

        $this->triggerEvent(
            'afterViewParseContent',
            array('view' => $this, 'page' => $this->getPage())
        );

        return $this;
    }

    public function __set($name, $value) {
        $this->items[$name] = $value;
    }
    /**
     * Custom getter
     * @param  string $name
     * @return mixed
     */
    public function __get($name) {
        if (array_key_exists($name, $this->items)) {
            return $this->items[$name];
        }
        elseif ($this->getBag()->has($name)) {
            return $this->getBag()->get($name);
        }

        /**
         * @todo  maybe add logging, exception will break the site if no meta
         */
        //throw new \Exception('Item '.$name.' could not be found.');
    }

    /**
     * Gets the value of page.
     *
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Sets the value of page.
     *
     * @param mixed $page the page
     *
     * @return self
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }
}