<?php
declare(strict_types=1);
namespace Cl\Container\Arrayable;

use Cl\Container\ContainerInterface;
use Cl\Container\Exception\DuplicateException;
use Cl\Container\Exception\InvalidArgumentException;

use Traversable;

class ArrayContainer implements ContainerInterface
{
    /**
     * @var array The arrayable storage.
     */
    protected $container = [];

    /**
     * @var int The count of items
     */
    protected $count = 0;

    /**
     * Constructor
     *
     * @param array $container The initial container
     * 
     * @throws InvalidArgumentException If the initial container is not a two-dimensional array.
     */
    public function __construct(array $container = [])
    {
        $this->container = $container;
    }
    
    /**
     * Attach an item to the container with a specified priority.
     *
     * @param mixed $item The item to attach.
     * 
     * @return void
     */
    public function attach(mixed $item): void 
    {
        $this->container[] = $item;
    }

    /**
     * Detatch the item
     *
     * @param mixed $item 
     * 
     * @return boolean True if successfully detached, false otherwise
     */
    public function detach(mixed $item): bool
    {
        if (false !== $has = $this->has($item, true)) {
            unset($this->container[$has]);
            $this->container = array_values($this->container);
            return true;
        }
        return false;
    }

    /**
     * Check if the container has a specific item.
     *
     * @param mixed $item The item to check for.
     * 
     * @return mixed 
     */
    public function has(mixed $item): mixed
    {
        return array_search($item, $this->container);
    }


    /**
     * Get the iterator for traversing the container.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        foreach ($this->container as $id => $item ) {
                yield $this->container[$id];
        }
    }
    /**
     * Gets the container items as iterator
     * 
     * @param int|string $id 
     *
     * @return mixed
     */
    public function get(int|string $id = null): mixed
    {
        return match (true) {
            null === $id => $this->container,
            default => $this->container[$id]
        };
    }

    /**
     * Gets the array 
     *
     * @return array
     */
    public function getArray(): array
    {
        return $this->get();
    }

    /**
     * Get the count of intems in container
     *
     * @return integer
     */
    public function count(): int
    {
        return count($this->container);
    }

    /**
     * Reset the container
     *
     * @return void
     */
    public function reset()
    {
        $this->container = [];
    }
}