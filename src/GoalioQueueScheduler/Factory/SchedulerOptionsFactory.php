<?php
namespace GoalioQueueScheduler\Factory;

use GoalioQueueScheduler\Options\SchedulerOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SchedulerOptionsFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        return new SchedulerOptions($config['slm_queue']['scheduler']);
    }
}