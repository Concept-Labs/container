<?php
namespace Ctl\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * The configuration paths constants 
     */
    const CONFIG_PATH_CONFIG = "config";
    const CONFIG_PATH_PREFERENCE = "preference";
    const CONFIG_PATH_DEBUG = 'debug';
    const CONFIG_PATH_ENABLED = "enabled";
    const CONFIG_PATH_LOG = "log";
    const CONFIG_PATH_FILE = "file";
    const CONFIG_PATH_POLICY = "policy";
    const CONFIG_PATH_USE_CLASS_ALIAS = "use-class-alias";

    /**
     * Create a new service instance
     *
     * @param string $id The service identifier.                      
     *                   May be a service name or a class name
     * 
     * //param mixed  ...$args The optional service constructor arguments
     * 
     * @return mixed
     */
    function new(string $id, ...$args);

    /**
     * Attach a service 
     *
     * @param string $id      The service identifier
     * @param mixed  $service The service instance or whatever else
     * 
     * @return void
     */
    function attach(string $id, $service): void;

}