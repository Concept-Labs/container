<?php
declare(strict_types=1);
namespace Cl\Container;


interface ContainerInterface
{
    function attach($item);
    function get(): mixed;
    //function has($id): bool;
}