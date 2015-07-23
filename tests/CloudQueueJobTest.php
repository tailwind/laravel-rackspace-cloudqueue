<?php
chdir(__DIR__);
include('../vendor/autoload.php');

use Mockery as m;

/**
 * Class CloudQueueTest
 */
class CloudQueueJobTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->mockedJob           = 'fooJob';
        $this->mockedData          = ['data'];
        $this->mockedPayload       = json_encode(['job' => $this->mockedJob, 'data' => $this->mockedData, 'attempts' => 1]);
        $this->mockedMessageId     = 'e3cd03ee-59a3-4ad8-b0aa-ee2e3808ac81';
        $this->mockedClaimId = 'fidfjksskndjdsfkjnsdknfsdks';
        $this->mockedJobData       = [
            'Body'          => $this->mockedPayload,
            'MD5OfBody'     => md5($this->mockedPayload),
            'MessageId'     => $this->mockedMessageId,
        ];

        $this->queueName = 'foobard';
    }

    public function testFireProperlyCallsTheJobHandler()
    {
        // Use Mockery to mock the IoC Container
        $container = m::mock('Illuminate\Container\Container');
        $container->shouldReceive('make')->once()->with('fooJob')
            ->andReturn($handler = m::mock('StdClass'));


        // Get a mock of the CloudQueue
        $this->mockedOpenCloudQueue = $this->getMockBuilder('OpenCloud\Queues\Resource\Queue')
                                           ->setMethods(['deleteMessages'])
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $cloudQueueMessage = m::mock('OpenCloud\Queues\Resource\Message')
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($this->mockedJobData['Body'])
            ->getMock();



        $job = new \Tailwind\RackspaceCloudQueue\Queue\Jobs\RackspaceCloudQueueJob(
            $container,
            $this->mockedOpenCloudQueue,
            $this->queueName,
            $cloudQueueMessage
        );

        $handler->shouldReceive('fire')->once()->with($job,['data']);

        $job->fire();
    }


    public function testDeleteRemovesTheJobFromQueue()
    {

        // Use Mockery to mock the IoC Container
        $container = m::mock('Illuminate\Container\Container');


        // Get a mock of the CloudQueue
        $this->mockedOpenCloudQueue = m::mock('OpenCloud\Queues\Resource\Queue')
            ->shouldReceive('deleteMessages')
            ->once()
            ->with([$this->mockedMessageId])
            ->getMock();


        $cloudQueueMessage = m::mock('OpenCloud\Queues\Resource\Message')
                              ->shouldReceive('getId')
                              ->once()
                              ->andReturn($this->mockedMessageId)
                              ->getMock();


        $job = new \Tailwind\RackspaceCloudQueue\Queue\Jobs\RackspaceCloudQueueJob(
            $container,
            $this->mockedOpenCloudQueue,
            $this->queueName,
            $cloudQueueMessage
        );


        $job->delete();
    }

    public function testReleaseProperlyReleasesTheJob()
    {

        // Use Mockery to mock the IoC Container
        $container = m::mock('Illuminate\Container\Container');


        // Get a mock of the CloudQueue
        $this->mockedOpenCloudQueue = m::mock('OpenCloud\Queues\Resource\Queue');


        $cloudQueueMessage = m::mock('OpenCloud\Queues\Resource\Message')
                              ->shouldReceive('getClaimIdFromHref','delete')
                              ->once()
                              ->andReturn($this->mockedClaimId,null)
                              ->getMock();


        $job = new \Tailwind\RackspaceCloudQueue\Queue\Jobs\RackspaceCloudQueueJob(
            $container,
            $this->mockedOpenCloudQueue,
            $this->queueName,
            $cloudQueueMessage
        );


        $job->release();
    }


    public function tearDown()
    {
        m::close();
    }
}
