<?php
declare(strict_types=1);
namespace Cl\Container\Test\Prioritized;

use Cl\Container\Iterator\Prioritized\PrioritizedContainer;
use PHPUnit\Framework\TestCase;

/**
 * @covers Cl\Container\Prioritized\PrioritizedContainer
 */
class PrioritizedContainerTest extends TestCase
{


    public static function assetItems()
    {
        return [
            [
                [
                    new class {
                        public $priority = 10;
                    },
                    $item2 = new class {
                        public $priority = 30;
                    },
                    $item3 = new class {
                        public $priority = 20;
                    },        
                    $item3 = new class {
                        public $priority = 20;
                    },        
                    $item3 = new class {
                        public $priority = 20;
                    },        
                ]
            ]
        ];
    }

    /**
     * 
     *
     * @dataProvider assetItems
     */
    public function testAttach($items): void
    {
        $container = new PrioritizedContainer();

        foreach ($items as $item) {
            $container->attach($item, $item->priority);
        }
        
        //print_r(iterator_to_array($container));die();

        $this->assertSame(
            array_values([$items[1], $items[2], $items[3], $items[4], $items[0]]),
            array_values(iterator_to_array($container))
        );
    }


    public function testHasItem(): void
    {
        $container = new PrioritizedContainer();
        $item = new \stdClass();
        $hash = $container->attach($item);
        $this->assertTrue($container->has($hash));
    }

    public function testHasNonExistingItem(): void
    {
        $container = new PrioritizedContainer();
        $item = new \stdClass();
        $this->assertFalse($container->has('non existing'));
    }

    public function testCountMethod(): void
    {
        $container = new PrioritizedContainer();
        $this->assertEquals(0, $container->count());

        $container->attach('test', 10);
        $this->assertEquals(1, $container->count());
    }

    public function testReset(): void
    {
        $container = new PrioritizedContainer();
        $container->attach('test');
        $this->assertCount(1, $container);
        $container->reset();
        $this->assertCount(0, $container);
    }

    public function testIterator(): void
    {
        $container = new PrioritizedContainer();

        $item1 = new \stdClass();
        $item2 = new \stdClass();
        $item3 = new \stdClass();

        $container->attach($item1, 10);
        $container->attach($item2, 20);
        $container->attach($item3, 30);

        /**
         * @var iterator $iterator
         */
        $iterator = $container->getIterator();
        // print_r($iterator);
        // die();

        $this->assertSame($item3, $iterator->current());
        $iterator->next();
        $this->assertSame($item2, $iterator->current());
        $iterator->next();
        $this->assertSame($item1, $iterator->current());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    public function testPreserveKeysTrue(): void
    {
        $container = new PrioritizedContainer();

        $item1 = new \stdClass();
        $item2 = new \stdClass();
        $item3 = new \stdClass();

        $container->attach($item1, 10);
        $container->attach($item2, 40);
        $container->attach($item3, 30);
        $this->assertSame([$item2, $item3, $item1], iterator_to_array($container->get(false)));
        $this->assertSame([$item2, $item3, $item1], array_values(iterator_to_array($container->get(false))));
        
    }

    public function testPreserveKeysFalse(): void
    {
        $container = new PrioritizedContainer();

        $item1 = new \stdClass();
        $item2 = new \stdClass();
        $item3 = new \stdClass();

        $container->attach($item1, 10);
        $container->attach($item2, 40);
        $container->attach($item3, 30);
        $this->assertNotSame([$item2, $item3, $item1], iterator_to_array($container->get()));
        $this->assertSame([$item2, $item3, $item1], array_values(iterator_to_array($container->get(false))));
        
    }
    
    public function testgetMultiple(): void
    {
        $container = new PrioritizedContainer();

        $item1 = new \stdClass();
        $item2 = new \stdClass();
        $item3 = new \stdClass();

        $hashes = [];
        $hashes[] = $container->attach($item1, 10);
        $hashes[] = $container->attach($item2, 40);
        $hashes[] = $container->attach($item3, 30);
        
        $this->assertSame(
            [$item1, $item2, $item3], 
            array_values(iterator_to_array($container->getByHash($hashes)))
        );
        
    }
}