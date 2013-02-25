<?php
namespace GoalioQueueScheduler\Job;

use SlmQueue\Queue\QueueInterface;

interface ScheduledJobInterface {

    public function getOptions();
    public function getHandles();

    public function setQueue(QueueInterface $queue);
    public function getQueue();

}