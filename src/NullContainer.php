<?php

declare(strict_types=1);
namespace Cl\Container;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class NullContainer implements ContainerInterface
{
    /**
     * Always null
     *
     * @param string $id 
     * 
     * @return void
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new class extends \Exception implements NotFoundExceptionInterface {};
        }
        return null;
    }
   
    /**
     * Container never has service
     *
     * @param string $id 
     * 
     * @return boolean
     */
    public function has(string $id): bool
    {
        return false;
    }
}
