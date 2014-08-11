<?php

namespace Plainmotif\Mizzenlite\Navigation;

use Plainmotif\Mizzenlite\Base;
use Knp\Menu\MenuFactory;
use Plainmotif\Mizzenlite\Page\PageInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
/**
 * @todo  move menu classes to the bag
 */
class Navigation extends Base
{
    /**
     * @var ItemInterface
     */
    private $menu;
    /**
     * @return string html
     */
    public function render()
    {
        if ($this->getMenu() instanceof ItemInterface === false) {
            throw new \InvalidArgumentException('Menu needs to conform to the
                ItemInterface (Knp\Menu\ItemInterface)'
            );
        }

        $matcher  = new Matcher();
        $matcher->addVoter(new UriVoter(
            (string) $this->getBag()->get('url')->getPath()
        ));
        $renderer = new ListRenderer($matcher);

        return $renderer->render($this->getMenu());
    }

    public function createPageMenu()
    {
        $factory = new MenuFactory();
        $pages   = $this->getBag()->get('pageRepository')->findAll();
        $menu    = $factory->createItem('root');
        $this->setMenuAttributes('root', $menu);

        foreach ($pages as $path => $page) {
            if (strpos($path, '/')) {
                $parents = explode('/', $path);
                $child   = array_pop($parents);
                $this->addToParent($menu, $parents, $child, $page);
            } else {
                $this->addToMenu($menu, $path, $page);
            }
        }

        $this->setMenu($menu);

        $this->triggerEvent('navigationCreatePageMenuAfter', array(
            'navigation' => $this,
        ));

        return $this;
    }

    protected function addToParent(ItemInterface $menu, array $parents, $child, PageInterface $page)
    {
        $this->triggerEvent('navigationAddToParentBefore', array(
            'navigation' => $this,
            'page'       => $page,
            'menu'       => $menu,
            'parents'    => &$parents,
            'child'      => &$child,
        ));

        foreach ($parents as $item) {
            $menu = $menu[$item];
        }
        $this->setMenuAttributes('parent', $menu);
        $this->addToMenu($menu, $child, $page);

        $this->triggerEvent('navigationAddToParentAfter', array(
            'navigation' => $this,
            'page'       => $page,
            'menu'       => $menu,
            'parents'    => &$parents,
            'child'      => &$child,
        ));
    }

    protected function addToMenu(ItemInterface $menu, $path, PageInterface $page)
    {
        $this->triggerEvent('navigationAddToMenuBefore', array(
            'navigation' => $this,
            'page'       => $page,
            'menu'       => $menu,
            'path'       => &$path,
        ));

        $menu->addChild($path);

        if (!$page->isDir()) {
            $menu[$path]->setUri($page->getPath());
        }

        $this->setMenuAttributes('child', $menu[$path]);
        $this->setMenuAttributes('link', $menu[$path]);
        $this->setMenuAttributes('label', $menu[$path]);

        $this->triggerEvent('navigationAddToMenuAfter', array(
            'navigation' => $this,
            'page'       => $page,
            'menu'       => $menu,
            'path'       => &$path,
        ));
    }

    public function setMenuAttributes($type, ItemInterface $menu)
    {
        if (isset($this->getBag()->get('config')->nav->attributes->{$type})) {
            $attributes = (array) $this->getBag()
                ->get('config')
                ->nav
                ->attributes
                ->{$type}
            ;
            switch ($type) {
                case 'root': // set attribs on top ul
                    $menu->setChildrenAttributes($attributes);
                    break;
                case 'parent': // set attribs on sub uls
                    $menu->setChildrenAttributes($attributes);
                    break;
                case 'child': // set attribs on li
                    $menu->setAttributes($attributes);
                    break;
                case 'link': // set attribs on a tags
                    $menu->setLinkAttributes($attributes);
                    break;
                case 'label': // set attribs on span tags
                    $menu->setLabelAttributes($attributes);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Gets the value of menu.
     *
     * @return ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Sets the value of menu.
     *
     * @param ItemInterface $menu the menu
     *
     * @return self
     */
    public function setMenu(ItemInterface $menu)
    {
        $this->menu = $menu;

        return $this;
    }
}