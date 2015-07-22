<?php namespace Tailwind\RackspaceCloudQueue;

use Illuminate\Support\ServiceProvider;
use Tailwind\RackspaceCloudQueue\Queue\Connectors\RackspaceCloudQueueConnector;

/**
 * Class RackspaceCloudQueueServiceProvider
 * @package Tailwind\RackspaceCloudQueue
 */
class RackspaceCloudQueueServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booted(function ($app) {

            $app['queue']->extend('rackspace', function () {
                return new RackspaceCloudQueueConnector;
            });

        });
    }
}