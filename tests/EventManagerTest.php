<?php

namespace Mizzencms\Core\Test;

use Mizzencms\Core\EventManager;
use Mizzencms\Core\Test\MockEventManager;

class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testObserversSetToEmptyArrayOnConstruct()
    {
        $this->assertTrue(is_array((new EventManager())->getObservers()));
        $this->assertEmpty((new EventManager())->getObservers());
    }

    public function testAttach()
    {
        $em       = new EventManager();
        $observer = $this->getMockForAbstractClass('Mizzencms\Core\Observer');

        $observer->setEvents(['event']);
        $em->attach($observer);
        $this->assertCount(1, $em->getObservers());
    }

    public function testNotify()
    {
        $em       = new EventManager();
        $observer = $this->getMockForAbstractClass('Mizzencms\Core\Observer');

        $observer->setEvents(['event']);
        $em->attach($observer);
        $em->notify('event', array('data' => 'hello'));
        $this->assertEquals('event', $observer->getTriggeredEvent());
        $this->assertEquals(array('data' => 'hello'), $observer->getTriggeredEventParams());
        $this->assertSame($em->getBag(), $observer->getBag(), 'Not the same instance.');

        // test run() method was called

        $mem = new MockEventManager();

        $observer->expects($this->any())
            ->method('run')
            ->will($this->returnValue(true))
        ;

        $mem->attach($observer);
        $this->assertTrue($mem->notify('event', array('data' => 'hello')));
    }
    /**
     * ATM detach leaves the event in the array even if there are no 
     * observers attached, thus the need for $before & $after
     */
    public function testDetach()
    {
        $em       = new EventManager();
        $observer = $this->getMockForAbstractClass('Mizzencms\Core\Observer');

        $observer->setEvents(['event']);
        $em->attach($observer);
        $this->assertCount(1, $em->getObservers());
        $before = $em->getObservers();
        $em->detach($observer);
        $after = $em->getObservers();
        $this->assertFalse(($before == $after));
    }
}