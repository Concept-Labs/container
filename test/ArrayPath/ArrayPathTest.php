<?php
use PHPUnit\Framework\TestCase;
use Cl\Container\ArrayPath\ArrayPath;
use Cl\Container\ArrayPath\ArrayPathInterface;
use Cl\Container\ArrayPath\Exception\InvalidPathException;

/**
 * @covers Cl\Container\ArrayPath\ArrayPath
 */
class ArrayPathTest extends TestCase
{
    protected ArrayPath $ArrayPath;
    public function ArrayPathDataProvider()
    {
        
    }

    
    public function setUp():void
    {
        $data = [
            'a' => ['b' => 'value'],
            'a.a' => ['b' => ["c.c" => 'value']],
            'childInstance' => ['a','b'],
            
        ];
        $this->ArrayPath =  new ArrayPath($data);
    }
    /**
     * Test the getter
     *
     * @return void
     */
    #[DataProvider('ArrayPathDataProvider')]
    public function testOffsetGetByKey()
    {
        $result = $this->ArrayPath->offsetGet('a');
        $this->assertSame(['b' => 'value'], $result->getArrayCopy());

        $result = $this->ArrayPath['a'];
        $this->assertSame(['b' => 'value'], $result->getArrayCopy());
    }

    /**
     * Test the getter using path string
     *
     * @return void
     */
    public function testOffsetGetByPath()
    {
        $result = $this->ArrayPath->offsetGet('a.b');
        $this->assertSame('value', $result);

        $result = $this->ArrayPath['a.b'];
        $this->assertSame('value', $result);
        
        $result = $this->ArrayPath->offsetGet('a');
        $this->assertInstanceOf(ArrayPathInterface::class, $result);
    }

    /**
     * Test the getter using path string
     *
     * @return void
     */
    public function testOffsetGetByPathReturnInstance()
    {
        $result = $this->ArrayPath->offsetGet('"a.a"');
        $this->assertInstanceOf(ArrayPathInterface::class, $result);
    }

    /**
     * Test the getter using path string
     *
     * @return void
     */
    public function testOffsetGetByPathSplitter()
    {
        $result = $this->ArrayPath->offsetGet('"a.a".b');
        $this->assertInstanceOf(ArrayPathInterface::class, $result);
        $this->assertSame(['c.c'=>'value'], (array)$result);
    }

    /**
     * Test the getter with invalid path string
     *
     * @return void
     */
    public function testOffsetGetInvalidPath()
    {
        $this->expectException(InvalidPathException::class);

        $this->ArrayPath->offsetGet('invalid.path');
    }

    /**
     * Test child instance
     *
     * @return void
     */
    public function testNewChildInstance()
    {
        $data = ['a' => ['b' => ['c','d'=>'e']]];
        $iterator = new ArrayPath($data);
        /** @var ArrayPath $child */
        $child = $iterator['a.b'];

        $this->assertInstanceOf(ArrayPath::class, $child);
        $this->assertSame(['c','d'=>'e'], $child->getArrayCopy());
        $this->assertSame('a.b', $child->getPath());
        $this->assertSame($iterator, $child->getParent());
        $this->assertSame(ArrayPath::PATH_DEFAULT_SEPARATOR, $child->getSeparator());
        $this->assertSame(0, $child->getFlags());
    }
}