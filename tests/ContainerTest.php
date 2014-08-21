<?php

namespace Strayobject\Mizzenlite\Test;

use Strayobject\Mizzenlite\Container;

/**
 * @todo test exceptions are raised
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    protected $bag;

    public function setUp()
    {
        $this->bag = Container::getInstance();
    }

    /**
     * We use Container::has() to verify if something was added. Not ideal.
     */
    public function testAdd()
    {
        $this->bag->add('test', function () { return 'testing'; });

        $this->assertTrue($this->bag->has('test'));
    }

    public function testGet()
    {
        $this->assertEquals('testing', $this->bag->get('test'));
    }

    public function testGetShared()
    {
        $this->bag->add('sharedTest', function () { return new \StdClass(); });

        $sharedTest         = $this->bag->getShared('sharedTest');
        $sharedTest->newVar = 'sharedTestVar';
        $sharedTestDouble   = $this->bag->getShared('sharedTest');

        $this->assertTrue(property_exists($sharedTestDouble, 'newVar'));
        $this->assertSame($sharedTest, $sharedTestDouble, 'Not the same instance.');
    }

    public function testHasPositive()
    {
        $this->assertTrue($this->bag->has('test'));
    }

    public function testHasNegative()
    {
        $this->assertFalse($this->bag->has('idontexist'));
    }

    public function testHasSharedPositive()
    {
        $this->assertTrue($this->bag->hasShared('sharedTest'));
    }

    public function testHasSharedNegative()
    {
        $this->assertFalse($this->bag->hasShared('idontexist'));
    }

    public function testRemove()
    {
        $this->bag->remove('test');

        $this->assertFalse($this->bag->has('test'));
    }

    public function testRemoveShared()
    {
        $this->bag->removeShared('sharedTest');

        $this->assertFalse($this->bag->hasShared('sharedTest'));
    }
}