<?php
declare(strict_types=1);
namespace Cl\Container\Arrayable\Priority;

use Cl\Able\Resettable\ResettableInterface;
use Cl\Container\Exception\DuplicateException;
use Cl\Container\Exception\InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class PriorityContainer implements IteratorAggregate, ResettableInterface
{
    /**
     * Arrayable storage
     *
     * @var array
     */
    protected $container = [];

    /**
     * Container is sorted flag
     *
     * @var boolean
     */
    protected bool $sorted =false;

    /**
     * The incrementable serial. Used for stable sorting items with same priorities
     *
     * @var integer
     */
    protected $serial = 0;


    /***
     * The minimal priority
     */
    const MIN_PRIORITY = 0;
    /**
     * The maximal priority
     */
    const MAX_PRIORITY = 10;
    /**
     * Th default priority
     */
    const DEFAULT_PRIORITY = 1;
    
    /**
     * Attach an item to the container with a specified priority.
     *
     * @param mixed $item            The item to attach.
     * @param float $priority        The priority of the item.
     * @param bool  $duplicate_check Ignore check for duplicates
     * 
     * @return void
     * @throws DuplicateException If the item is already present in the container.
     */
    public function attach(
        mixed $item,
        float $priority = self::DEFAULT_PRIORITY,
        ?bool $duplicate_check = true
    ): void {
        // Ensure priority is within valid range
        $priority = $priority >= static::MAX_PRIORITY ? $priority : static::MAX_PRIORITY;
        $priority = $priority <= static::MIN_PRIORITY ? $priority : static::MIN_PRIORITY;

        // Check for duplicates
        if ($this->has($item) && $duplicate_check) {
            throw new DuplicateException;
        }

        // Create priority bucket if not exists
        if (!$this->hasPriority($priority)) {
            $this->container[(string)$priority] = [];
        }

        // Add item with a serial key for stable sorting
        $this->container[(string)$priority][(string)($this->serial+=0.0000001)] = $item;
    }

    /**
     * Sort the container by priority and serial key.
     *
     * @return void
     */
    public function sort(): void
    {
        if (!$this->sorted) {
            // Sort individual priority buckets
            foreach ($this->container as $priority => $items) {
                ksort($this->container[$priority], SORT_NUMERIC);
            }

            // Sort priority buckets by their keys (priorities)
            ksort($this->container, SORT_NUMERIC);

            $this->sorted = true;
        }
    }

    /**
     * Check if the container has a specific item.
     *
     * @param mixed $item The item to check for.
     * 
     * @return bool True if the item is present, false otherwise.
     */
    public function has(mixed $item): bool
    {
        // Search for the item in all priority buckets
        foreach ($this->container as $prioritized) {
            if (false !== array_search($item, $prioritized)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the container has a specific priority.
     *
     * @param float $priority The priority to check for.
     * 
     * @return bool True if the priority exists, false otherwise.
     */
    protected function hasPriority(float $priority): bool
    {
        if ($priority < static::MIN_PRIORITY || $priority > static::MAX_PRIORITY) {
            throw new InvalidArgumentException('Invalid priority value');
        }
        return array_key_exists((string)$priority, $this->container);
    }

    /**
     * Get the iterator for traversing the container.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        $this->sort();
        foreach ($this->container as $priority => $items ) {
            foreach ($this->container[$priority] as $item) {
                yield $item;
            }
        }
    }

    /**
     * Reset the container
     *
     * @return void
     */
    public function reset()
    {
        $this->container = [];
        $this->sorted = false;
        $this->serial = 0;
    }
}