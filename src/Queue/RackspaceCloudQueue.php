<?php namespace Tailwind\RackspaceCloudQueue\Queue;

use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueInterface;
use OpenCloud\Common\Constants\Datetime;
use OpenCloud\Queues\Resource\Queue as OpenCloudQueue;
use OpenCloud\Queues\Service as OpenCloudService;
use RuntimeException;
use Tailwind\RackspaceCloudQueue\Queue\Jobs\RackspaceCloudQueueJob;

/**
 * Class RackspaceCloudQueue
 * @package Tailwind\RackspaceCloudQueue\Queue
 */
class RackspaceCloudQueue extends Queue implements QueueInterface
{

    /**
     * The Rackspace OpenCloud Message Service instance.
     *
     * @var OpenCloudService
     */
    protected $openCloudService;

    /**
     * The Rackspace OpenCloud Queue instance
     *
     * @var OpenCloudQueue
     */
    protected $queue;

    /**
     * The name of the default tube.
     *
     * @var string
     */
    protected $default;

    /**
     * @param OpenCloudService $openCloudService
     * @param                  $default
     * @throws \OpenCloud\Common\Exceptions\InvalidArgumentError
     */
    public function  __construct(OpenCloudService $openCloudService, $default)
    {
        $this->openCloudService = $openCloudService;
        $this->default          = $default;
        $this->queue            = $openCloudService->createQueue($default);
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string $job
     * @param  mixed  $data
     * @param  string $queue
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string $payload
     * @param  string $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = array())
    {
        $ttl = array_key_exists('ttl', $options) ? $options['ttl'] : Datetime::DAY * 2;

        return $this->queue->createMessage(
            array(
                'body' => $payload,
                'ttl'  => $ttl,
            )
        );
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTime|int $delay
     * @param string        $job
     * @param string        $data
     * @param null          $queue
     * @return mixed|void
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        throw new RuntimeException('RackspaceCloudQueue::later() method is not supported');
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string $queue
     * @return \Illuminate\Queue\Jobs\Job|null|RackspaceCloudQueueJob
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        /**
         * @var \OpenCloud\Common\Collection\PaginatedIterator $response
         */
        $response = $this->queue->claimMessages(
            array(
                'grace' => 5 * Datetime::MINUTE,
                'ttl'   => 5 * Datetime::MINUTE,
            ));

        if ( $response and $response->valid() ) {
            $message = $response->current();

            return new RackspaceCloudQueueJob($this->container, $this->queue, $queue, $message);
        }
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null $queue
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ?: $this->default;
    }

    /**
     * Get the underlying OpenCloud Queue instance.
     *
     * @return OpenCloudQueue
     */
    public function getOpenCloudQueue()
    {
        return $this->queue;
    }
}
