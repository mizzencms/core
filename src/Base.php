<?php

namespace Strayobject\Mizzenlite;

use Strayobject\Mizzenlite\Helpers\StringHelper;

abstract class Base
{
    private $bag;

    public function __construct()
    {
        $this->setBag(Container::getInstance());

        if ($this->getBag()->has('eventManager')
            && get_class($this) != 'Strayobject\Mizzenlite\EventManager')
        {
            $this->triggerEvent(
                'construct'.ucfirst(StringHelper::toCamelCase(get_class($this), '\\')),
                array('class' => $this)
            );
        }
    }

    public function triggerEvent($eventName, $eventParams)
    {
        //d($eventName);
        if ($bag = $this->getBag()) {
            $bag->getShared('eventManager')->notify($eventName, $eventParams);
        }
    }

    public function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    /**
     * Gets the value of bag.
     *
     * @return mixed
     */
    public function getBag()
    {
        return $this->bag;
    }

    /**
     * Sets the value of bag.
     *
     * @param mixed $bag the bag
     *
     * @return self
     */
    public function setBag(Container $bag)
    {
        $this->bag = $bag;

        return $this;
    }
}
