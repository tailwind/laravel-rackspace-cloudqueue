<?php namespace Faulker\RackspaceCloudQueue\Queue\Connectors;

use Faulker\RackspaceCloudQueue\Queue\RackspaceCloudQueue;
use OpenCloud\Rackspace;
use OpenCloud\Queues\Service;
use Illuminate\Queue\Connectors\ConnectorInterface;

class RackspaceCloudQueueConnector implements ConnectorInterface {

    /**
     * @var \OpenCloud\Rackspace
     */
    protected $connection = null;

    /**
     * @var \OpenCloud\Queues\Service
     */
    protected $service = null;

    /**
     * Establish a queue connection.
     *
     * @param  array $config
     * @return \Illuminate\Queue\QueueInterface
     */
    public function connect(array $config)
    {
        switch ($config['endpoint'])
        {
            case 'US':
                $endpoint = Rackspace::US_IDENTITY_ENDPOINT;
                break;
            case 'UK':
            default:
                $endpoint = Rackspace::UK_IDENTITY_ENDPOINT;
        }

        if ($this->connection == null)
        {
            $this->connection = new Rackspace(
                $endpoint,
                array(
                    'username' => $config['username'],
                    'apiKey' => $config['apiKey']
                )
            );
        }

        if ($this->service === null)
        {
            $this->service = $this->connection->queuesService(
                Service::DEFAULT_NAME,
                $config['region'],
                $config['urlType']
            );
        }

        $this->service->setClientId();

        return new RackspaceCloudQueue($this->service, $config['queue']);
    }

}
