<?php
declare(strict_types=1);
namespace Cl\Container\Test\Arrayable\Priority;


use Cl\Container\Arrayable\Priority\PriorityContainer;
use Cl\Container\Exception\DuplicateException;
use PHPUnit\Framework\TestCase;

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

    public function testAttachItemWithDuplicateCheck(): void
    {
        $container = new PriorityContainer();
        $item = 'example';

        $container->attach($item);

        // Attempting to attach the same item again should throw DuplicateException
        $this->expectException(DuplicateException::class);
        $container->attach($item);
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

    public function testReset(): void
    {
        $container = new PriorityContainer();
        $item = 'example';

        $container->attach($item);

        // Resetting the container
        $container->reset();

        // Container should be empty after resetting
        $this->assertFalse($container->has($item));
    }
}
