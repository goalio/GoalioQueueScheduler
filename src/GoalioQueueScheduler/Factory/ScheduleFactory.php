<?php
namespace GoalioQueueScheduler\Factory;

use Zend\ServiceManager\FactoryInterface;
use GoalioQueueScheduler\Queue\Schedule;
use GoalioQueueDoctrine\Queue\Table;
use Zend\ServiceManager\ServiceLocatorInterface;

class ScheduleFactory implements FactoryInterface {

    protected $queueName;

    public function __construct($queueName) {
        $this->queueName = $queueName;
    }

    /**
      *  {@inheritDoc}
      */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = '', $requestedName = '')
    {
        $parentLocator = $serviceLocator->getServiceLocator();

        /**
         * @var $doctrineOptions \GoalioQueueScheduler\Options\SchedulerOptions
         */
        $schedulerOptions = $parentLocator->get('GoalioQueueScheduler\Options\SchedulerOptions');

        /**
         * @var $connection \Doctrine\DBAL\Connection
         */
        $connection         = $parentLocator->get($schedulerOptions->getConnection());
        $tableName          = $schedulerOptions->getTableName();
        $jobPluginManager   = $parentLocator->get('SlmQueue\Job\JobPluginManager');
        $queuePluginManager = $parentLocator->get('SlmQueue\Queue\QueuePluginManager');
        $queue              = $queuePluginManager->get($this->queueName);

        $schedule = new Schedule($queue, $connection, $tableName, $requestedName, $jobPluginManager);

        $schedule->setBuriedLifetime($schedulerOptions->getBuriedLifetime());
        $schedule->setDeletedLifetime($schedulerOptions->getDeletedLifetime());
        $schedule->setScheduleAhead($schedulerOptions->getScheduleAhead());

        return $schedule;
    }
}