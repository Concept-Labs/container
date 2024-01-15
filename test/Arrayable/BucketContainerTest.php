<?php
declare(strict_types=1);
namespace Cl\Container\Test\Arrayable;

use Cl\Container\Arrayable\BucketContainer;
use Cl\Container\ContainerInterface;
use Cl\Container\Arrayable\Exception\SectionNotFoundException;
use Cl\Container\Arrayable\Priority\PriorityContainer;
use PHPUnit\Framework\TestCase;

/**
 * @covers Cl\Container\Arrayable\BucketContainer
 */
class BucketContainerTest extends TestCase
{
    public function testAttachAndGet()
    {
        $containerA = $this->createMock(ContainerInterface::class);
        $containerB = $this->createMock(ContainerInterface::class);

        $bucketContainer = new BucketContainer();
        $bucketContainer->attach('sectionA', $containerA);
        $bucketContainer->attach('sectionB', $containerB);

        $this->assertSame($containerA, $bucketContainer->get('sectionA'));
        $this->assertSame($containerB, $bucketContainer->get('sectionB'));
    }

    public function testAttachWithSectionNotFoundException()
    {
        $this->expectException(SectionNotFoundException::class);

        $bucketContainer = new BucketContainer();
        $bucketContainer->get('nonExistentSection');
    }

    public function testRemove()
    {
        $containerA = $this->createMock(ContainerInterface::class);
        $containerB = $this->createMock(ContainerInterface::class);

        $bucketContainer = new BucketContainer();
        $bucketContainer->attach('sectionA', $containerA);
        $bucketContainer->attach('sectionB', $containerB);

        $this->assertTrue($bucketContainer->remove('sectionA'));
        $this->assertFalse($bucketContainer->has('sectionA'));
    }

    public function testRemoveWithSectionNotFoundException()
    {
        $this->expectException(SectionNotFoundException::class);

        $bucketContainer = new BucketContainer();
        $bucketContainer->remove('nonExistentSection');
    }

    public function testClear()
    {
        $containerA = new PriorityContainer();
        $containerB = new PriorityContainer();

        $bucketContainer = new BucketContainer();
        $bucketContainer->attach('sectionA', $containerA);
        $bucketContainer->attach('sectionB', $containerB);
        $bucketContainer->clear();

        $this->assertSame(0, $bucketContainer->count());
    }

    public function testCountWithRealContainer()
    {
        $containerA = new PriorityContainer();
        $containerA->attach('Aitem1');
        $containerB = new PriorityContainer();
        $containerB->attach('Bitem1');
        $containerB->attach('Bitem2');

        $bucketContainer = new BucketContainer();
        $bucketContainer->attach('sectionA', $containerA);
        $bucketContainer->attach('sectionB', $containerB);
        $this->assertSame(3, $bucketContainer->count());
    }

}
