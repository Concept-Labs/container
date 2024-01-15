<?php
declare(strict_types=1);
namespace Cl\Container\Arrayable\Priority;

use Cl\Container\Arrayable\ArrayContainer;
use Cl\Container\ContainerInterface;
use Cl\Container\Exception\DuplicateException;
use Cl\Container\Exception\InvalidArgumentException;

use Traversable;

class PriorityContainer extends ArrayContainer 
    implements ContainerInterface
{
    /**
     * @var array The arrayable storage.
     */
    protected $container = [];

    /**
     * @var bool The flag indicating whether the container is sorted.
     */
    protected bool $sorted =false;

    /**
     * @var int The count of items
     */
    protected $count = 0;

    /**
     * @var int The incrementable serial used for stable sorting items with the same priorities.
     */
    protected $serial = 999999;


    /***
     * The minimal priority
     */
    const MIN_PRIORITY = 0;
    /**
     * The maximal priority
     */
    const MAX_PRIORITY = 100;
    /**
     * Th default priority
     */
    const DEFAULT_PRIORITY = 1;
    

    /**
     * Constructor
     *
     * @param array $container The initial container
     * 
     * @throws InvalidArgumentException If the initial container is not a two-dimensional array.
     */
    public function __construct(?array $container = [])
    {
        if (count($container)) {
            foreach ($container as $key => $items) {
                if (!is_array($items)) {
                    throw new InvalidArgumentException(
                        _("Initial container must be two-deminition array")
                    );
                }
            }
        }
        $this->container = $container;
    }
    
    /**
     * Attach an item to the container with a specified priority.
     *
     * @param mixed $item             The item to attach.
     * @param int   $priority         The priority of the item.
     * @param bool  $duplicate_check  Ignore check for duplicates
     * @param bool  $resort_on_attach Sort after attach
     * 
     * @return void
     * @throws DuplicateException If the item is already present in the container.
     */
    public function attach(
        mixed $item,
        float $priority = self::DEFAULT_PRIORITY,
        bool $duplicate_check = true,
        bool $resort_on_attach = true
    ): void {
        // Ensure priority is within valid range
        $priority = $priority >= static::MIN_PRIORITY ? $priority : static::MIN_PRIORITY;
        $priority = $priority <= static::MAX_PRIORITY ? $priority : static::MAX_PRIORITY;

        // Check for duplicates
        if ($duplicate_check && $this->has($item)) {
            // $item = $this->container[$has['priority']][$has['key']];
            // if (is_callable($item)|| is_object($item)) {
            //     throw new DuplicateException;
            // }
            throw new DuplicateException;
        }

        // Create priority bucket if not exists
        if (!$this->hasPriority($priority)) {
            $this->container[$priority] = [];
        }

        // Add item with a serial key for stable sorting
        $this->container[$priority][--$this->serial] = $item;

        $this->sorted = false;
        if ($resort_on_attach) {
            $this->sort();
        }
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
            unset($this->container[$has['priority']][$has['key']]);
            $this->sorted = false;
            return true;
        }
        return false;
    }

    /**
     * Check if the container has a specific item.
     *
     * @param mixed $item The item to check for.
     * 
     * @return array|bool [priority => key] array if the item is present, false otherwise.
     */
    public function has(mixed $item, bool $return_lookup = false): array|bool
    {
        // Search for the item in all priority buckets
        foreach ($this->container as $priority => $items) {
            if (false !== $key = array_search($item, $items)) {
                return $return_lookup ? ['priority' => $priority, 'key' => $key] : true;
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
            throw new InvalidArgumentException(
                sprintf(_("Priority must be in range %d...%d"), static::MIN_PRIORITY, static::MAX_PRIORITY)
            );
        }
        return array_key_exists($priority, $this->container);
    }

    /**
     * Sort the container by priority and serial key.
     *
     * @return int Count of items in container
     */
    public function sort(): int
    {
        if (!$this->sorted) {
            //Reset count
            $this->count = 0;

            // Sort individual priority buckets
            foreach ($this->container as $priority => $items) {

                krsort($this->container[$priority], SORT_NUMERIC);

                //Update count
                $this->count += count($this->container[$priority]);
            }
            
            // Sort priority buckets by their keys (priorities)
            krsort($this->container, SORT_NUMERIC);
            
            $this->sorted = true;
        }
        
        return $this->count;
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
            foreach ($this->container[$priority] as $key => $item) {
                yield $this->container[$priority][$key];
            }
        }
    }
    /**
     * Gets the container items as iterator
     *
     * @return array
     */
    public function get(int|string $id = null): array
    {
        return $this->getArray();
    }

    /**
     * Gets the array in priority order
     *
     * @return array
     */
    public function getArray(): array
    {
        return iterator_to_array($this);
    }

    /**
     * Get the count of intems in container
     *
     * @return integer
     */
    public function count(): int
    {
        return $this->sort();
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