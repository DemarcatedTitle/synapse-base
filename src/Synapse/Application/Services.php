<?php

namespace Synapse\Application;

use Synapse\Application;

/**
 * Define services
 */
class Services implements ServicesInterface
{
    /**
     * {@inheritDoc}
     * @param  Application $app
     */
    public function register(Application $app)
    {
        // Register log provider first to catch any exceptions thrown in the others
        $app->register(new \Synapse\Log\ServiceProvider);

        // Register security component before other providers attempt to extend $app['security.firewalls']
        $app->register(new \Silex\Provider\SecurityServiceProvider);
        $this->registerSecurityFirewalls($app);

        $this->registerServiceProviders($app);
    }

    /**
     * Register service providers
     *
     * @param  Application $app
     */
    protected function registerServiceProviders(Application $app)
    {
        $app->register(new \Synapse\Command\CommandServiceProvider);
        $app->register(new \Synapse\Db\DbServiceProvider);
        $app->register(new \Synapse\OAuth2\ServerServiceProvider);
        $app->register(new \Synapse\OAuth2\SecurityServiceProvider);
        $app->register(new \Synapse\Resque\ResqueServiceProvider);
        $app->register(new \Synapse\Controller\ControllerServiceProvider);
        $app->register(new \Synapse\Email\EmailServiceProvider);
        $app->register(new \Synapse\User\UserServiceProvider);
        $app->register(new \Synapse\Migration\MigrationServiceProvider);
        $app->register(new \Synapse\Upgrade\UpgradeServiceProvider);
        $app->register(new \Synapse\Session\SessionServiceProvider);
        $app->register(new \Synapse\SocialLogin\SocialLoginServiceProvider);

        $app->register(new \Synapse\View\ViewServiceProvider, [
            'mustache.paths' => array(
                APPDIR.'/templates'
            ),
            'mustache.options' => [
                'cache' => TMPDIR,
            ],
        ]);

        $app->register(new \Silex\Provider\ValidatorServiceProvider);
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider);

        // Register the CORS middleware
        $app->register(new \JDesrosiers\Silex\Provider\CorsServiceProvider);
        $app->after($app['cors']);
    }

    /**
     * Register the security firewalls for use with the Security Context in SecurityServiceProvider
     *
     * How to add application-specific firewalls:
     *
     *     $app->extend('security.firewalls, function ($firewalls, $app) {
     *         $newFirewalls = [...];
     *
     *         return array_merge($newFirewalls, $firewalls);
     *     });
     *
     * It's important to return an array with $firewalls at the end, as in the example,
     * so that the catch-all 'base.api' firewall does not preclude more specific firewalls.
     *
     * Application-specific firewalls should only be needed to allow passthrough
     * for public endpoints, since 'base.api' requires authentication.
     *
     * @param  Application $app
     */
    public function registerSecurityFirewalls(Application $app)
    {
        $app['security.firewalls'] = $app->share(function () {
            return [
                'base.api' => [
                    'pattern'   => '^/',
                    // Order of oauth and anonymous matters!
                    'oauth'     => true,
                    'anonymous' => true,
                ],
            ];
        });
    }
}
