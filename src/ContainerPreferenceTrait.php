<?php
namespace Ctl\Container;

use Ctl\Container\Preference\Preference;
use Ctl\Container\Preference\PreferenceInterface;

/**
 * PSR Container
 */
trait ContainerPreferenceTrait
{
    /**
     * Get the preference instance based on configuration
     *
     * @param string $id The preference identifier
     * 
     * @return PreferenceInterface
     */
    protected function getPreference(string $id): PreferenceInterface
    {
        
        $preferenceConfigData = $this->getConfigValue(
            ContainerInterface::CONFIG_PATH_PREFERENCE,
            $id
        );
        
        /**
         * @var PreferenceInterface $preference The preference object
         */
        $preference = Preference::withConfig(
            $this->getConfig()
                ->withData(
                    array_merge(
                        /**
                         * Preference configuration may be absent
                         * so the id may be used as the class name
                         */
                        [
                            PreferenceInterface::CONFIG_PATH_ID => $id,
                            PreferenceInterface::CONFIG_PATH_CLASS => $id
                        ],
                        is_array($preferenceConfigData) ? $preferenceConfigData : []
                    )
                )
        );
        
        /**
         * A preference can refer to the another one
         */
        if (null !== $preference->getReference()) {

            $referer = $preference;

            $preference = $this->getPreference($preference->getReference());

            $preference->setReferer($referer);
        }

        $this->applyPreferenceConfig($preference);
        
        return  $preference;
    }


    /**
     * A preferernce may contain config with container addons/overrides
     * see config JSON:
     *  {
     *    <preference>: {
     *      "container-config":{...<config>...}
     *    }
     * }
     *
     * @param PreferenceInterface $preference The preference
     * 
     * @return void
     */
    protected function applyPreferenceConfig(PreferenceInterface $preference): void
    {
        if (true === $preference->hasContainerConfig()) {
            /**
             * Save the current state of the container
             */
            $this->pushState();

            /**
             * Apply the preference configuration
             */
            $this->getConfig()->mergeFrom($preference->getContainerConfigData());
        }
    }

    /**
     * Proceed preference`s lifecycle
     *
     * @param PreferenceInterface $preference The preference instance
     * 
     * @return void
     */
    protected function preferenceLifeCycle(PreferenceInterface $preference)
    {
        /**
         * Find an original preference by checking a referers chain
         */
        $finalPreference = $preference;

        /**
         * Get a root referer
         */
        while ($preference->hasReferer()) {

            $preference = $preference->getReferer();

            /**
             * Restore a container state if the preference did override
             */
            if ($preference->hasContainerConfig()) {
                $this->popState();
            }
        }

        /**
         * Make a class alias
         * As far as the preference ID may be not an existing class name
         * This avoid a constructor parameter type error
         * @TODO:VG generate the code with class/interface declaration instead, 
         * so IDE can resolve them ?
         */
        if ($finalPreference->getClass() !== $preference->getClass()) {
            if (true === $this->getConfigValue(
                ContainerInterface::CONFIG_PATH_POLICY,
                ContainerInterface::CONFIG_PATH_USE_CLASS_ALIAS
            )
            ) {
                class_alias($finalPreference->getClass(), $preference->getId());
            }

        }

        /**
         * If the preference config has addons/overrides than 
         * restore a previous state of the container`s configuration
         */
        if ($finalPreference->hasContainerConfig()) {
            $this->popState();
        }

        /**
         * If the preference configured as singleton than register the service 
         */
        if ($finalPreference->isSingleton()) {
            $this->attach($preference->getId(), $finalPreference->getService());
        }
    }
}