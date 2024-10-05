<?php
namespace Concept\Container;

use Concept\Container\Exception\NotFoundException;

class Container implements ContainerInterface
{
    /**
     * @todo Implement not array sevice container?
     */
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
     * @return self
     */
    public function attach(string $id, $service): self
    {
        $this->services[$id] = $service;

        return $this;
    }
}