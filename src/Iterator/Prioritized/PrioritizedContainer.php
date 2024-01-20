<?php
declare(strict_types=1);
namespace Cl\Container\Iterator\Prioritized;

use Countable;
use Traversable;

final class PrioritizedContainer implements PrioritizedContainerInterface, Countable
{
    /**
     * @var array<string|int, mixed> $container 
     *      Container, organized by tag and priority
     */
    protected array $container = [];

    /**
     * @var int 
     *  The unique value used as a key to map items 
     *      from a container to a priority map.
     *  This value is shared among all instances of this class 
     *      and is crucial for correct sorting and uniqueness when the items are received by handlers.
     */
    protected static $uniq = 999999;

    /**
     * Generate a unique hash for accurate sorting by attachment order.
     *
     * @param int $priority The priority of the item.
     *
     * @return string The generated hash.
     */
    protected function generateHash(int $priority): string
    {
        return number_format($priority + --static::$uniq/1000000, 6);
    }

    /**
     * Attach an item to the container with a specified priority.
     *
     * Generates a hash for accurate sorting by the order of attachment.
     *
     * @param mixed    $item     The item to attach to the container.
     * @param int|null $priority The priority of the item (default is 0).
     *
     * @return int|string The hash identifier for the attached item.
     */
    public function attach(mixed $item, ?int $priority = null): int|string
    {
        $priority = $priority ?? static::DEFAULT_PRIORITY;
        

        /**
         * Generate hash for accurate sorting by attach turn
         */

         $hash = $this->generateHash($priority);

        /**
         * Attach item
         */
        $this->container[$hash] = $item;

        /**
         * Sort after attaching
         */
        $this->sort();

        return $hash;
    }

    /**
     * Returns the items in the priority order
     * 
     * @return Traversable
     */
    public function get(bool $preserve_keys = true): iterable
    {
        /**
         * Lookup for refrence in the map container 
         */
        foreach ($this->container as $priority => $item) {
            //yield $preserve_keys ? $priority : $priority => $item;

            match (true) {
                true == $preserve_keys => yield $priority => $item,
                default => yield $item,
            };
        };
    }

    /**
     * As far as attach() method returns unique id of added item
     *  container handler can receive items by know unique hash
     * Hash indicates priority composition if self::HASH_ENABLED is false
     *  otherwise priority may be unknown
     * 
     * @param array<string|int> $hashes The hashes array
     * 
     * @return void
     */
    public function getByHash(array $hashes): Traversable
    {
        foreach ($hashes as $hash) {
            if ($this->has($hash)) {
                yield $this->container[$hash];
            }
        }
    }

    /**
     * Returns the internal container representation.
     *
     * @return array<int, <int, mixed>>
     */
    public function getContainerRaw(): array
    {
        return $this->container;
    }

    /**
     * Check if an item exists in the container
     *
     * @param mixed $item The item to search for
     * 
     * @return bool True if the item exists, false otherwise
     */
    public function has(string $hash = null): bool
    {
        return !empty($this->container[$hash]);
    }
    
    /**
     * Sorts the itemss map based on their priority.
     *
     * This ensures that listeners are processed in the correct order.
     *
     * @return void
     */
    public function sort(?array &$array = null): void
    {
        
        /**
         * Sorth the container keys wich means by priority 
         * DESC order
         */
        if (null !== $array) {
            krsort($array);
        } else {
            krsort($this->container, SORT_NUMERIC);
        }
    }

    /**
     * Counter
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->container);
    }

    /**
     * Reset
     *
     * @return void
     */
    public function reset(): void
    {
        /**
         * static::$uniq is not ressetable
         */
        $this->container = [];
    }

    /**
     * Returns the items in the priority order
     * 
     * {@inheritDoc}
     */
    public function getIterator(): Traversable
    {
        yield from $this->get();
    }
}