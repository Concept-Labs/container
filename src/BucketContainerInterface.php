<?php
declare(strict_types=1);
namespace Cl\Container;

use Cl\Able\Resettable\ResettableInterface;
use Countable;
use IteratorAggregate;

interface BucketContainerInterface extends Countable, IteratorAggregate, ResettableInterface
{
    function attach(string $section, ContainerInterface $item): void;
    function has(string $section);
    function get(string $section): ContainerInterface;
}