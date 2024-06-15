<?php
namespace Concept\Container;

use Psr\Container\ContainerInterface;
use Concept\Container\Exception\NotFoundException;

class Container implements ContainerInterface
{
    protected array $services = [];

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if ($id === ContainerInterface::class) {
            return $this;
        }

        if (!$this->has($id)) {
            throw new NotFoundException("Service not found: $id");
        }

        return $this->services[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function has($id): bool
    {
        if ($id === ContainerInterface::class) {
            return true;
        }
        return isset($this->services[$id]);
    }

    /**
     * Attach a service to the container
     * 
     * @param string $id
     * @param mixed $service
     * 
     * @return void
     */
    public function attach(string $id, $service): void
    {
        $this->services[$id] = $service;
    }
}