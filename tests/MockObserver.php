<?php

namespace Mizzencms\Core\Test;

use Mizzencms\Core\Observer;

class MockObserver extends Observer
{
    public function __construct()
    {
        $this->setEvents(array('event'));
    }

    public function run()
    {
        var_dump('run');
    }
}