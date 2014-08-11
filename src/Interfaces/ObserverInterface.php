<?php

namespace Plainmotif\Mizzenlite\Interfaces;

interface ObserverInterface
{
    public function run();

    public function getEvents();

    public function setEvents($events);

    public function getTriggeredEvent();

    public function setTriggeredEvent($triggeredEvent);

    public function getTriggeredEventParams();

    public function setTriggeredEventParams($triggeredEventParams);
}