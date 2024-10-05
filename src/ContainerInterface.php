<?php
namespace Concept\Container;

interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    /**
     * Attach a service to the container
     * 
     * @param string $id
     * @param mixed $service
     * 
     * @return void
     */
    public function attach(string $id, $service): self;
}