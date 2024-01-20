<?php
declare(strict_types=1);
namespace Cl\Container\Test\Prioritized;

use Cl\Container\Iterator\Prioritized\PrioritizedTaggedContainer;
use PHPUnit\Framework\TestCase;

/**
 * @covers Cl\Container\Prioritized\PrioritizedTaggedContainer
 */
class PrioritizedTaggedContainerTest extends TestCase
{
    public function testContainer()
    {
        $container = new PrioritizedTaggedContainer();

        $item1 = new class {
            public $id = 't1t2 item 1';
            public $priority = 10;
        };
        $item11 = new class {
            public $id = '11t1 item 11';
            public $priority = 10;
        };
        $item2 = new class {
            public $id = 't1t3 item 2';
            public $priority =  20;
        };
        $item3 = new class {
            public $id = 't2t3 item 3';
            public $priority =  30;
        };
        $item4 = new class {
            public $id = 'untagged 4';
            public $priority =  25;
        };
        $item5 = new class {
            public $id = 'untagged 5';
            public $priority =  35;
        };

        $container->attach($item1, ['tag1', 'tag2'], $item1->priority);
        $container->attach($item11, ['tag1'], $item1->priority);
        $container->attach($item2, ['tag1', 'tag3'], $item2->priority);
        $container->attach($item3, ['tag2', 'tag3'], $item3->priority);
        

        $container->attach($item5, ['untagged'], $item5->priority);

        $this->assertSame(
            [$item2, $item1, $item11],
            iterator_to_array($container->get('tag1', false))
        );
        $this->assertSame(
            [$item3, $item1], 
            iterator_to_array($container->get('tag2'), false)
        );


        $this->assertSame(
            [$item3, $item2], 
            iterator_to_array($container->get('tag3'), false)
        );

        $this->assertSame(
            [$item3, $item2, $item1, $item11], 
            iterator_to_array($container->get(['tag3', 'tag1'], false))
        );

        // W/o tags (untagged)
        $this->assertSame(
            [$item5], 
            array_values(iterator_to_array($container->get('untagged')))
        );

        $this->assertSame(
            [
                'tag1' => [
                    $item2, $item1, $item11
                ],
                'tag2' => [
                    $item3, $item1,
                ],
                'tag3' => [
                    $item3, $item2,
                ],
                'untagged' => [
                    $item5
                ],
            ],
            iterator_to_array($container->getMultipleGrouped([], false))
        );

        $this->assertEquals(5, $container->count());
        
        $container->reset();
        $this->assertEquals(0, $container->count());

        $this->expectException(\Cl\EventDispatcher\ListenerProvider\Exception\InvalidArgumentException::class);
        //invalid tag
        $container->attach($item4, [], $item4->priority);
    }
}