<?php

use PHPUnit\Framework\TestCase;
use Cl\Container\Queue\PriorityQueue;

/**
 * @covers 
 */
class PriorityQueueTest extends TestCase
{
    public function testInsertWithDifferentPriorities()
    {
        $priorityQueue = new PriorityQueue();

        $priorityQueue->insert('Item A', 2);
        $priorityQueue->insert('Item B', 3);
        $priorityQueue->insert('Item C', 2);

        $this->assertEquals('Item B', $priorityQueue->extract());
        $this->assertEquals('Item A', $priorityQueue->extract());
        $this->assertEquals('Item C', $priorityQueue->extract());
    }

    public function testInsertWithSamePriorities()
    {
        $priorityQueue = new PriorityQueue();

        $priorityQueue->insert('Item A', 3);
        $priorityQueue->insert('Item B', 3);
        $priorityQueue->insert('Item C', 3);

        $this->assertEquals('Item A', $priorityQueue->extract());
        $this->assertEquals('Item B', $priorityQueue->extract());
        $this->assertEquals('Item C', $priorityQueue->extract());
    }

    public function testInsertWithSamePrioritiesInOrder()
    {
        $priorityQueue = new PriorityQueue();

        $priorityQueue->insert('Item A', 3);
        $priorityQueue->insert('Item B', 3);
        $priorityQueue->insert('Item C', 3);

        $this->assertEquals('Item A', $priorityQueue->extract());
        $this->assertEquals('Item B', $priorityQueue->extract());
        $this->assertEquals('Item C', $priorityQueue->extract());
    }
}