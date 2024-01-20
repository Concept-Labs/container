<?php
namespace Cl\Container\Test\Arrayable;

use Cl\Container\Arrayable\ArrayContainer;
use PHPUnit\Framework\TestCase;

/**
 * @covers Cl\Container\Arrayable\ArrayContainer
 */
class ArrayContainerTest extends TestCase
{
    public function testAttachAndGet()
    {
        $arrayContainer = new ArrayContainer();
        $arrayContainer->attach('itemA');
        $arrayContainer->attach('itemB');

        $this->assertSame(['itemA', 'itemB'], $arrayContainer->getArray());
    }

    public function testDetach()
    {
        $arrayContainer = new ArrayContainer();
        $arrayContainer->attach('itemA');
        $arrayContainer->attach('itemB');

        $arrayContainer->detach('itemA');
        $this->assertSame(['itemB'], $arrayContainer->getArray());
    }

    public function testDetachNonExistentItem()
    {
        $arrayContainer = new ArrayContainer();
        $arrayContainer->attach('itemA');

        $arrayContainer->detach('nonExistentItem');
        $this->assertSame(1, $arrayContainer->count());
    }

    public function testHas()
    {
        $arrayContainer = new ArrayContainer();
        $arrayContainer->attach('itemA');
        $arrayContainer->attach('itemB');

        $this->assertSame(0, $arrayContainer->has('itemA'));
        $this->assertSame(1, $arrayContainer->has('itemB'));
        $this->assertFalse($arrayContainer->has('nonExistentItem'));
    }

    public function testGetById()
    {
        $arrayContainer = new ArrayContainer(['itemA', 'itemB']);

        $this->assertSame('itemA', $arrayContainer->get(0));
        $this->assertSame('itemB', $arrayContainer->get(1));
        $this->assertNull($arrayContainer->get(2));
    }

    public function testGetAll()
    {
        $arrayContainer = new ArrayContainer(['itemA', 'itemB']);

        $this->assertSame(['itemA', 'itemB'], $arrayContainer->getArray());
    }

    public function testCount()
    {
        $arrayContainer = new ArrayContainer(['itemA', 'itemB']);

        $this->assertCount(2, $arrayContainer);
    }

    public function testReset()
    {
        $arrayContainer = new ArrayContainer(['itemA', 'itemB']);
        $arrayContainer->reset();

        $this->assertSame([], $arrayContainer->getArray());
    }

    // Додайте інші тести, які вам потрібні, згідно зі сценаріями використання
}