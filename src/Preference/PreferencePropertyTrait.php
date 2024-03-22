<?php
namespace Ctl\Container\Preference;

use Ctl\Config\ConfigInterface;

trait PreferencePropertyTrait
{

    /**
     * @var object|null The resolved service
     */
    protected ?object $service = null;
    
    /**
     * @var PreferenceInterface|null The referer
     */
    protected ?PreferenceInterface $referer = null;
    
    /**
     * {@inheritDoc}
     */
    public function getService(): object
    {
        return $this->service;
    }

    /**
     * {@inheritDoc}
     */
    public function setService($service): void
    {
        $this->service = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): string
    {
        return $this->getConfigValue(
            PreferenceInterface::CONFIG_PATH_ID
        );
    }

    /**
     * {@inheritDoc}
     */
    public function hasReferer(): bool
    {
        return $this->referer instanceof PreferenceInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * {@inheritDoc}
     */
    public function setReferer(PreferenceInterface $referer): void
    {
        /**
         * Save the current config state
         */
        $this->config->pushState();
        /**
         * Merge a configuration from referer`s configuration 
         * So a referer may use the same configuration except:
         */
        $refererConfigData = $referer->getConfigValue("");
        unset(//@TODO:VG merge path?
            $refererConfigData[PreferenceInterface::CONFIG_PATH_ID],  
            $refererConfigData[PreferenceInterface::CONFIG_PATH_CLASS],
            $refererConfigData[PreferenceInterface::CONFIG_PATH_REFERENCE],
        );
        $this->getConfig()->mergeFrom($refererConfigData);
        
        $this->referer = $referer;
    }

    /**
     * {@inheritDoc}
     */
    public function getReference()
    {
        return $this->getConfigValue(
            PreferenceInterface::CONFIG_PATH_REFERENCE
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->getConfigValue(
            PreferenceInterface::CONFIG_PATH_CLASS
        );
    }


    /**
     * {@inheritDoc}
     */
    function hasContainerConfig(): bool
    {
        return null !== $this->getConfigValue(
            PreferenceInterface::CONFIG_PATH_CONTAINER_CONFIG
        );
    }
    
    /**
     * {@inheritDoc}
     */
    function getContainerConfigData()
    {
        return $this->getConfigValue(
            PreferenceInterface::CONFIG_PATH_CONTAINER_CONFIG
        );
    }

    /**
     * {@inheritDoc}
     */
    public function isSingleton(): bool
    {
        return null !== $this->getConfigValue(
            PreferenceInterface::CONFIG_PATH_SINGLETON
        )
            ? $this->getConfigValue(PreferenceInterface::CONFIG_PATH_SINGLETON) 
            : false;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameterConfigData(string $name)
    {
        return $this->getConfigValue(
            PreferenceInterface::CONFIG_PATH_PARAMETERS,
            $name
        );
    }

}