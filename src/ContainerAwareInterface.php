<?php
namespace Concept\Container;

interface ContainerAwareInterface
{
    public function setContainer(ContainerInterface $container): void;
}