<?php
declare(strict_types=1);
namespace Cl\Container;

use Cl\Able\Resettable\ResettableInterface;
use IteratorAggregate;
use Countable;

interface ContainerInterface extends Countable, ResettableInterface, IteratorAggregate
{
    function attach($item): void;
    function has($item);
}