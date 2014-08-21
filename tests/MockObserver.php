<?php

namespace Strayobject\Mizzenlite\Test;

use Strayobject\Mizzenlite\Observer;

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