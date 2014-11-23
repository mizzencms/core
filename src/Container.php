<?php

namespace Mizzencms\Core;

class Container
{
    private $items;
    private $itemsShared;
    private static $instance;

    private function __construct()
    {
        $this->setItems([]);
        $this->setItemsShared([]);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Container;
        }

        return self::$instance;
    }

    public function add($key, \Closure $value)
    {
        $this->setItems(array($key => $value) + $this->getItems());
    }

    public function remove($key)
    {
        if (!$this->has($key)) {
            throw new \Exception('Item '.$key.' has not been found in a bag.');
        }

        $items = $this->getItems();

        unset($items[$key]);
        $this->setItems($items);
    }

    public function removeShared($key)
    {
        if (!$this->has($key)) {
            throw new \Exception('Item '.$key.' has not been found in a bag.');
        }

        $items = $this->getItemsShared();

        unset($items[$key]);
        $this->setItemsShared($items);
    }

    public function get($key)
    {
        if (!$this->has($key)) {
            throw new \Exception('Item '.$key.' has not been found in a bag.');
        }

        $items = $this->getItems();

        return $items[$key]();
    }

    public function getShared($key)
    {
        if ($this->hasShared($key)) {
            // nothing special at the moment
        }
        elseif ($this->has($key)) {
            $this->setItemsShared(array($key => $this->get($key)) + $this->getItemsShared());
        }
        else {
            throw new \Exception('Item '.$key.' has not been found in a bag.');
        }

        return $this->getItemsShared()[$key];
    }

    public function has($key)
    {
        return array_key_exists($key, $this->getItems());
    }

    public function hasShared($key)
    {
        return array_key_exists($key, $this->getItemsShared());
    }

    /**
     * Gets the value of items.
     *
     * @return mixed
     */
    protected function getItems()
    {
        return $this->items;
    }

    /**
     * Sets the value of items.
     *
     * @param mixed $items the items
     *
     * @return self
     */
    protected function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Gets the value of itemsShared.
     *
     * @return mixed
     */
    protected function getItemsShared()
    {
        return $this->itemsShared;
    }

    /**
     * Sets the value of itemsShared.
     *
     * @param mixed $itemsShared the items shared
     *
     * @return self
     */
    protected function setItemsShared($itemsShared)
    {
        $this->itemsShared = $itemsShared;

        return $this;
    }

    /**
     * Sets the value of instance.
     *
     * @param mixed $instance the instance
     *
     * @return self
     */
    protected function setInstance($instance)
    {
        $this->instance = $instance;

        return $this;
    }
}