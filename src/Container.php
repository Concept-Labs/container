<?php
namespace Concept\Container;

use Concept\Container\Exception\NotFoundException;
use Concept\Factory\FactoryInterface;
use Concept\Prototype\NonPrototypableInterface;
use Concept\Prototype\PrototypableInterface;

class Container implements ContainerInterface, NonPrototypableInterface
{
    /**
     * @todo Implement not array sevice container?
     */
    protected array $services = [];

    public function withFactory(FactoryInterface $factory): self
    {
        if ($factory instanceof ContainerAwareInterface) {
            $factory->setContainer($this);
        }
        $this->attach(FactoryInterface::class, $factory);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    // public function get($id)
    // {
    //     if ($id === ContainerInterface::class) {
    //         return $this;
    //     }

    //     if (!$this->has($id)) {
    //         try {

    //             $service =  $this->createService($id);

    //         } catch (\Throwable $e) {
    //             throw new NotFoundException("Service not found or could not be created: $id", 0, $e);
    //         }
    //     } else {
    //         $service = $this->services[$id];
    //         if ($service instanceof PrototypableInterface) {
    //             //or return immediately?
    //             $service = clone $service;
    //         }
    //     }

        

    //     return $service;
    // }

    public function get($id)
    {
        if ($id === ContainerInterface::class) {
            return $this;
        }

        $service = $this->getFromRegistry($id) ?? $this->createService($id);

        return $this->handlePrototype($service);
    }

    protected function getFromRegistry($id)
    {
        return $this->services[$id] ?? null;
    }

    protected function handlePrototype($service)
    {
        if ($service instanceof PrototypableInterface) {
            return clone $service;
        }
        return $service;
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
        if ($this->has($id)) {
            throw new \RuntimeException("Service already exists: $id");
        }
        $this->services[$id] = $service;

        return $this;
    }

    protected function createService(string $id)
    {
        return $this->getFactory()->create($id);
    }

    protected function getFactory(): FactoryInterface
    {
        $factory = $this->get(FactoryInterface::class);

        if (!$factory instanceof FactoryInterface) {
            throw new \RuntimeException('Factory not found');
        }

        return $factory;
    }
}