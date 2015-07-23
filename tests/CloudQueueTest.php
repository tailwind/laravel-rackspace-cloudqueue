<?php
chdir(__DIR__);
include('../vendor/autoload.php');

use Mockery as m;

/**
 * Class CloudQueueTest
 */
class CloudQueueTest extends PHPUnit_Framework_TestCase
{

    /** @var  \Mockery\MockInterface */
    protected $cloudQueueService;

    public function setUp()
    {
        $this->cloudQueueService = m::mock('OpenCloud\Queues\Service');
        $this->default           = 'default';

    }

    public function testPopProperlyPopsJobOff()
    {
        $message = m::mock('OpenCloud\Queues\Resource\Message');

        $response = m::mock('OpenCloud\Common\Collection\PaginatedIterator')
                     ->shouldReceive(['valid'=>true, 'current'=>$message])
                     ->getMock();

        $openCloudQueue = m::mock('OpenCloud\Queues\Resource\Queue')
                           ->shouldReceive('claimMessages')
                           ->andReturn($response)
                           ->getMock();

        $this->cloudQueueService->shouldReceive('createQueue')->once()->andReturn($openCloudQueue)->getMock();

        $queue = new \Tailwind\RackspaceCloudQueue\Queue\RackspaceCloudQueue(
            $this->cloudQueueService,
            $this->default
        );
        $queue->setContainer(m::mock('Illuminate\Container\Container'));

        $job = $queue->pop();

        $this->assertInstanceOf('Tailwind\RackspaceCloudQueue\Queue\Jobs\RackspaceCloudQueueJob', $job);
    }

    public function testPushProperlyPushesJobOn()
    {
        $job = m::mock('Tailwind\RackspaceCloudQueue\Queue\Jobs\RackspaceCloudQueueJob');

        $openCloudQueue = m::mock('OpenCloud\Queues\Resource\Queue')
                           ->shouldReceive('createMessage')
                           ->andReturn(true)
                           ->getMock();

        $this->cloudQueueService->shouldReceive('createQueue')->once()->andReturn($openCloudQueue)->getMock();

        $queue = new \Tailwind\RackspaceCloudQueue\Queue\RackspaceCloudQueue(
            $this->cloudQueueService,
            $this->default
        );
        $queue->setContainer(m::mock('Illuminate\Container\Container'));

        $this->assertTrue($queue->push($job));

    }

    public function tearDown()
    {
        m::close();
    }
}
