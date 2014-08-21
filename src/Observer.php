<?php
/**
 * @package mizzenlite
 * @author Michal Zdrojewski <code@strayobject.co.uk>
 * @copyright 2014 Michal Zdrojewski
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://mizzencms.net
 */
namespace Strayobject\Mizzenlite;

use Strayobject\Mizzenlite\Interfaces\ObserverInterface;

abstract class Observer extends Base implements ObserverInterface
{
    private $events;
    private $triggeredEvent;
    private $triggeredEventParams;

    /**
     * Gets the value of events.
     *
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Sets the value of events.
     *
     * @param mixed $events the events
     *
     * @return self
     */
    public function setEvents($events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * Gets the value of triggeredEvent.
     *
     * @return mixed
     */
    public function getTriggeredEvent()
    {
        return $this->triggeredEvent;
    }

    /**
     * Sets the value of triggeredEvent.
     *
     * @param mixed $triggeredEvent the triggered event
     *
     * @return self
     */
    public function setTriggeredEvent($triggeredEvent)
    {
        $this->triggeredEvent = $triggeredEvent;

        return $this;
    }

    /**
     * Gets the value of triggeredEventParams.
     *
     * @return mixed
     */
    public function getTriggeredEventParams()
    {
        return $this->triggeredEventParams;
    }

    /**
     * Sets the value of triggeredEventParams.
     *
     * @param mixed $triggeredEventParams the triggered event params
     *
     * @return self
     */
    public function setTriggeredEventParams($triggeredEventParams)
    {
        $this->triggeredEventParams = $triggeredEventParams;

        return $this;
    }
}