<?php
declare(strict_types=1);
namespace Cl\Container\Test\Arrayable\Priority;


use Cl\Container\Arrayable\Priority\PriorityContainer;
use Cl\Container\Exception\DuplicateException;
use PHPUnit\Framework\TestCase;

/**
 * @covers Cl\Container\Arrayable\Priority\PriorityContainer
 */
class PriorityContainerTest extends TestCase
{
    public function testAttachItem(): void
    {
        $container = new PriorityContainer();
        $item = 'example';
        $priority = 5;

        $container->attach($item, $priority);

        $this->assertTrue($container->has($item));
    }

    public function testAttachItemWithDefaultPriority(): void
    {
        $container = new PriorityContainer();
        $item = 'example';

        $container->attach($item);

        $this->assertTrue($container->has($item));
    }

    public function testPriorityAttach(): void
    {
        $container = new PriorityContainer();
        $item = 'example';
        $item1 = 'example1';
        $item2 = 'example2';
        $item3 = 'example3';

        $container->attach($item, 1);
        $container->attach($item1, 1);
        $container->attach($item2, 7);
        $container->attach($item3, 2);

        $sortedItems = iterator_to_array($container->get());
        $this->assertEquals('example2', $sortedItems[0]);
    }

    public function testDetatchItemFromContainer()
    {
        $container = new PriorityContainer();
        $item = 'example_item';
        $priority = 5;

        $container->attach($item, $priority);
        $container->detach($item);

        $this->assertFalse($container->has($item));
    }

    public function testAttachItemWithDuplicateCheck(): void
    {
        $container = new PriorityContainer();
        $item = 'example';

        $container->attach($item);

        // Attempting to attach the same item again should throw DuplicateException
        $this->expectException(DuplicateException::class);
        $container->attach($item);
        $this->assertSame(1, $container->count());
    }
    
    public function testAttachItemWithoutDuplicateCheck(): void
    {
        $container = new PriorityContainer();
        $item = 'example';

        $container->attach($item);
        $container->attach($item, 1, false);
        $this->assertTrue($container->has($item));
        $this->assertSame(2, $container->count());
    }

    public function testAttachCallableWithDuplicateCheck(): void
    {
        $container = new PriorityContainer();
        $item = function () {
            return true;
        };
        $item2 = function () {
            return false;
        };

        $container->attach($item);
        $container->attach($item2);
        // Attempting to attach the same item again should throw DuplicateException
        $this->expectException(DuplicateException::class);
        $container->attach($item);
        $container->attach($item2);
    }

    public function testAttachCallableWithoutDuplicateCheck(): void
    {
        $container = new PriorityContainer();
        $item = function () {
            return true;
        };
        $item2 = function () {
            return false;
        };

        $container->attach($item);
        $container->attach($item2);
        
        $container->attach($item, 1, false);
        $container->attach($item2, 2, false);

        $this->assertTrue($container->has($item));
        $this->assertTrue($container->has($item2));
        $this->assertSame(4, $container->count());
    }

    public function testSort(): void
    {
        $container = new PriorityContainer();
        $items = ['item1', 'item3', 'item2'];

        foreach ($items as $item) {
            $container->attach($item);
        }

        // Sorting the container
        $container->sort();

        // Getting the first item after sorting should be 'item1'
        $sortedItems = iterator_to_array($container->getIterator());
        $this->assertEquals('item1', $sortedItems[0]);
    }

    public function testResetAndSameValue(): void
    {
        $container = new PriorityContainer();
        $item = 'example';
        $item2 = 'example';

        $container->attach($item);
        $container->attach($item2, 1, false);

        // Container should be empty after resetting
        $this->assertSame(2, $container->count());
        // Resetting the container
        $container->reset();

        // Container should be empty after resetting
        $this->assertSame(0, $container->count());
    }
}
