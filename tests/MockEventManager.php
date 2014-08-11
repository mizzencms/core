<?php

namespace Plainmotif\Mizzenlite\Test;

use Plainmotif\Mizzenlite\EventManager;

class MockEventManager extends EventManager
{
    /**
     * For testing wee need to return Observer::run() value
     */
    public function notify($event, $params)
    {
        $observers = $this->getObservers();

        if (isset($observers[$event])) {
            foreach ($observers[$event] as $observer) {
                $observer->setTriggeredEvent($event);
                $observer->setTriggeredEventParams($params);
                $observer->setBag($this->getBag());
                return $observer->run();
           }
        }
    }
}