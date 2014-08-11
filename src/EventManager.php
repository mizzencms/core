<?php

namespace Plainmotif\Mizzenlite;

use Plainmotif\Mizzenlite\Interfaces\ObserverInterface;

class EventManager extends Base
{
    /**
     * Holds an array of observers
     * @var array
     */
    private $observers;

    public function __construct()
    {
        parent::__construct();

        $this->setObservers([]);
    }
    /**
     * Method responsible for adding observers to event manager
     * @todo fail if observer has no events
     * @param  ObserverInterface $observer
     */
    public function attach(ObserverInterface $observer)
    {
        $observers = $this->getObservers();

        foreach ($observer->getEvents() as $event) {
            $observers[$event][] = $observer;
        }

        $this->setObservers($observers);
    }
    /**
     * Method responsible for removing observers from event manager
     * @param  ObserverInterface $observer
     */
    public function detach(ObserverInterface $observer)
    {
        $observers = $this->getObservers();

        foreach ($observer->getEvents() as $event) {
            $count = count($observers[$event]);

            for ($i = 0; $i < $count; $i++) {
                if ($observers[$event][$i] == $observer) {
                    unset($observers[$event][$i]);
                }
            }
        }

        $this->setObservers($observers);
    }
    /**
     * Method responsible for notifying observers upon event trigger
     * @param  string $event
     * @param  array $params
     */
    public function notify($event, $params)
    {
        $observers = $this->getObservers();

        if (isset($observers[$event])) {
            foreach ($observers[$event] as $observer) {
                $observer->setTriggeredEvent($event);
                $observer->setTriggeredEventParams($params);
                $observer->setBag($this->getBag());
                $observer->run();
           }
        }
    }

    /**
     * Gets the value of observers.
     *
     * @return mixed
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     * Sets the value of observers.
     *
     * @param mixed $observers the observers
     *
     * @return self
     */
    public function setObservers($observers)
    {
        $this->observers = $observers;

        return $this;
    }
}