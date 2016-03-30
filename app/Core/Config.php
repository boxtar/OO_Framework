<?php

namespace App\Core;

use App\Core\Utilities\Singleton;

// TODO: Allow configuration options to contain arrays themselves.

class Config extends Singleton
{

    /*
     |-----------------------------------------
     | Path to configuration files
     |-----------------------------------------
     */
    protected $configPath;

    /*
     |-----------------------------------------
     | Path to configuration files
     |-----------------------------------------
     */
    protected $loadedConfigFiles = [];


    protected function __construct()
    {
        parent::__construct();

        $this->setConfigPath();

        $this->getConfigFiles();
    }

    protected function getConfigFiles(){

    }

    public function get($config)
    {
        $config = explode('.', $config);

        // TODO: allow config options to contain arrays. Meaning the count of $config would be >= 2
        if( count($config) !== 2)
            throw new \Exception('Invalid number of parameters passed to Config::get');

        if( $this->checkIfConfigLoaded($config) )
            return $this->loadedConfigFiles[$config[0]][$config[1]];

        if (! file_exists( $configFile = $this->getConfigPath() . '/' . $config[0] . '.php' ) )
            throw new \Exception('Configuration file not found: ' . $configFile);

        if (! $this->checkIfConfigFileIsValid( $configuration = require_once $configFile ))
            throw new \Exception('Configuration file is not valid: ' . $configFile);

        if (! $this->checkConfigOptionExists($config[1], $configuration) )
            throw new \Exception('Configuration option \'' . $config[1] . '\' not found');

        if (! is_array($configuration) || ! isset($configuration[$config[1]]) )
            throw new \Exception('Configuration file could not be opened: ' . $configFile);

        $this->loadedConfigFiles[$config[0]] = $configuration;

        return $this->loadedConfigFiles[$config[0]][$config[1]];
    }

    protected function checkIfConfigLoaded(array $config)
    {
        if ( isset( $this->loadedConfigFiles[$config[0]] ) )
        {
            if (! isset( $this->loadedConfigFiles[$config[0]][$config[1]] ) )
                throw new \Exception('Configuration option \'' . $config[1] . '\' not found');

            return true;
        }
        return false;
    }

    public function getConfigPath()
    {
        return $this->configPath;
    }

    public function setConfigPath($path='')
    {
        $this->configPath = empty($path) ? realpath(__DIR__ . '/../../config') : $path;

        return $this;
    }

    protected function checkIfConfigFileIsValid($config)
    {
        return is_array($config) ? true : false;
    }

    private function checkConfigOptionExists($option, $config)
    {
        return isset ( $config[$option] ) ? true : false;
    }

}