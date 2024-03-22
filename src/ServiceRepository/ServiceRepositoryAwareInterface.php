<?php
namespace Ctl\Container\ServiceRepository;

use Ctl\Container\ServiceRepository\ServiceRepositoryInterface;

interface ServiceRepositoryAwareInterface
{
    /**
     * Set a service repository
     *
     * @param ServiceRepositoryInterface $serviceRepository The service repository
     * 
     * @return void
     */
    function setServiceRepository(ServiceRepositoryInterface $serviceRepository): void;

}