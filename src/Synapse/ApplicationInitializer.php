<?php

namespace Synapse;

use Synapse\Config\ServiceProvider as ConfigServiceProvider;
use Symfony\Component\Debug\Debug;

/**
 * Object for initializing the Silex application
 */
class ApplicationInitializer
{
    /**
     * Initialize the Silex Application
     *
     * Register services and routes
     * Set basic properties
     *
     * @return Synapse\Application
     */
    public function initialize()
    {
        // Create the application object
        $app = new Application;

        $this->setEnvironment($app);
        $this->registerConfig($app);

        // Handle init config
        $initConfig = $app['config']->load('init');

        if ($initConfig['debug']) {
            Debug::enable();
            $app['debug'] = true;
        }

        return $app;
    }

    /**
     * Set $app['environment'] based on $_SERVER['APP_ENV']
     *
     * @param Application $app
     */
    protected function setEnvironment(Application $app)
    {
        // Define acceptable environments
        $environments = array(
            'production',
            'staging',
            'qa',
            'testing',
            'development',
            'beta',
        );

        if (! isset($_SERVER['APP_ENV'])) {
            throw new \Exception('Missing \'APP_ENV\' environment variable');
        }

        if (! in_array($_SERVER['APP_ENV'], $environments)) {
            throw new \Exception('Invalid \'APP_ENV\' environment variable: \'' + $_SERVER['APP_ENV'] +'\'');
        }

        $app['environment'] = $_SERVER['APP_ENV'];
    }

    /**
     * Register the config service
     *
     * Config is a bit of a special-case service provider and needs to be
     * registered before all the others (so that they can access it)
     *
     * @param  Application $app
     */
    protected function registerConfig(Application $app)
    {
        $app->register(new ConfigServiceProvider(), array(
            'config_dirs' => array(
                APPDIR.'/config/',
                APPDIR.'/config/'.$app['environment'].'/',
            ),
        ));
    }
}
