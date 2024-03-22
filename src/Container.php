<?php
namespace Ctl\Container;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

use Ctl\Config\ConfigInterface;
use Ctl\Config\ConfigAwareInterface;
use Ctl\Config\ConfigAwareTrait;
use Ctl\Container\Resolver\ResolverInterface;
use Ctl\Container\ServiceRepository\ServiceRepositoryAwareInterface;
use Ctl\Container\ServiceRepository\ServiceRepositoryAwareTrait;
use Ctl\Container\Preference\PreferenceInterface;

use Ctl\Container\Exception\InvalidArgumentException;
use Ctl\Container\Exception\NotFoundException;

use Ctl\Container\ServiceRepository\ServiceRepositoryInterface;
use Ctl\EventDispatcher\EventDispatcherAwareTrait;
use Ctl\EventDispatcher\ListenerProviderAwareTrait;
use Ctl\Debug\DebuggerAwareTrait;

/**
 * PSR Container
 */
class Container 
    implements 
        ContainerInterface,
        ConfigAwareInterface,
        ServiceRepositoryAwareInterface,
        LoggerAwareInterface
{
    use ContainerStateTrait;
    use ContainerCallStackTrait;
    use ContainerPreferenceTrait;
    
    use ConfigAwareTrait;
    use ServiceRepositoryAwareTrait;
    use LoggerAwareTrait;
    use EventDispatcherAwareTrait;
    use ListenerProviderAwareTrait;
    use DebuggerAwareTrait;

    /**
     * The constructor
     * 
     * @param ConfigInterface $config The configuration. Mandatory
     */
    public function __construct(ConfigInterface $config)
    {
        /**
         * Init the configuration
         */
        $this->setConfig($config);

        /**
         * Init the service repository
         */
        $this->serviceRepository = $this->new(ServiceRepositoryInterface::class);
        
        /**
         * Attach self as very first service
         */
        $this->attach(\Psr\Container\ContainerInterface::class, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function attach(string $id, $service): void
    {
        $this->getServiceRepository()->attach($id, $service);
    }
    
     /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return $this->getServiceRepository()->has($id);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id/*, ...$args*/): object
    {

        if (true === $this->has($id)) {
            
            $service = $this->getServiceRepository()->get($id);
            
        } else {
            
            $service = $this->new($id/*, ...$args*/);
        }

        if (null === $service) {
            throw new NotFoundException(
                sprintf(_('Service "%s" not found'), $id)
            );
        }

        return $service;
    }

    /**
     * {@inheritDoc}
     */
    public function new(string $id, ...$args): object
    {
        
        if ($this->callStackContains($id)) {
            /**
             * Avoid the recursion during resolving a service
             */
            throw new InvalidArgumentException(
                sprintf(_('Cannot instantiate "%s" recursively'), $id)
            );
        }

        $this->callStackPush($id);

        try {
            $service = null;
            /**
             * Resolve a preference (config) for a service looking for
             * 
             * @var PreferenceInterface $preference
             */
            $preference = $this->getPreference($id);

            /**
             * @var ResolverInterface $resolver
             */
            $resolver = $preference->getResolver(...$args);

            /**
             * Get the service instance
             */
            $service = ($resolver)($this);
            
            $this->preferenceLifeCycle($preference);

        } catch (\Throwable $e) {
            throw new NotFoundException(
                sprintf(
                    _("Unable to create the service \"%s\": %s \n%s"),
                    $id,
                    $e->getMessage(),
                    $e->getTraceAsString()
                ),
                $e->getCode(),
                $e
            );
        }

        $this->callStackPop($id);

        return $service;
    }

    /**
     * Reset the container instance
     *
     * @return void
     */
    public function reset()
    {
        $this->config->reset();
        $this->getServiceRepository()->reset();
    }
}