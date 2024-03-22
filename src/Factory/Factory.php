<?php
namespace Ctl\Container\Factory;

//use Psr\Container\ContainerInterface;
use Ctl\Container\ContainerInterface;// as CtlContainerInterface;

class Factory implements FactoryInterface
{

    /**
     * @var ContainerInterface|null The container
     */
    protected ?ContainerInterface $container = null;

    /**
     * @var string|null The service identifier
     */
    protected ?string $serviceId = null;

    /**
     * @var array|null The service constructor parameters
     */
    protected ?array $args = [];

    /**
     * The constructor
     *
     * @param ContainerInterface $container The factory
     * @param string             $serviceId The service identifier
     * @param array              ...$args   The service constructor parameters
     */
    public function __construct(ContainerInterface $container, string $serviceId, ...$args)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
        $this->args = $args;
    }

    /**
     * Get the container
     * 
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function new(...$args)
    {
        return $this->getContainer()
            ->new($this->serviceId, ...(!empty($args) ?? $this->args));
    }

    /**
     * {@inheritDoc}
     */
    public function create(...$args)
    {
        return $this->new(...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function ___invoke(...$args)
    {
        return $this->new(...($args));
    }

    /**
     * {@inheritDoc}
     */
    public function getFactory(...$args): \Closure
    {
        return \Closure::bind(
            function () use ($args) {
                return $this->new(...$args);
            },
            $this
        );
    }
}