<?php namespace Tailwind\RackspaceCloudQueue;

use Illuminate\Support\ServiceProvider;
use Tailwind\RackspaceCloudQueue\Queue\Connectors\RackspaceCloudQueueConnector;

/**
 * Class RackspaceCloudQueueServiceProvider
 * @package Tailwind\RackspaceCloudQueue
 */
class RackspaceCloudQueueServiceProvider extends ServiceProvider
{

		$this->app->booted(function () {
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

			Queue::extend('rackspacecloudqueue', function () {
				return new RackspaceCloudQueueConnector;
			});

        });
    }
}