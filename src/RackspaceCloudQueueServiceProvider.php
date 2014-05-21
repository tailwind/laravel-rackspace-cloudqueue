<?php namespace cchiles\RackspaceCloudQueue;

use cchiles\RackspaceCloudQueue\Queue\Connectors\RackspaceCloudQueueConnector;
use Illuminate\Support\ServiceProvider;
use Queue;

class RackspaceCloudQueueServiceProvider extends ServiceProvider
{

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->booted(function () {

			Queue::extend('rackspacecloudqueue', function () {
				return new RackspaceCloudQueueConnector;
			});

		});
	}
}