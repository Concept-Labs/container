<?php
declare(strict_types=1);
namespace Cl\Container\Iterator\Prioritized;

use Cl\Container\Iterator\ContainerIteratorInterface;
use Traversable;

interface PrioritizedTaggedContainerInterface extends ContainerIteratorInterface
{
    public function attach($item, ?array $tags = [], ?int $priority = null);

    public function get(string|array $tag = null): Traversable;
}
