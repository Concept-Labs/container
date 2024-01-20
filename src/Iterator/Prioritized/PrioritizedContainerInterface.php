<?php
declare(strict_types=1);
namespace Cl\Container\Iterator\Prioritized;

use Cl\Container\Iterator\ContainerIteratorInterface;

interface PrioritizedContainerInterface extends ContainerIteratorInterface
{
    /**
     * Default priority for container
     * Priority counting as higher as int value is higher
     */
    const DEFAULT_PRIORITY = 0;
    
    
    public function attach($item, ?int $priority = null);
    public function get(bool $preserve_keys = true): iterable;
}
