<?php
namespace GoalioQueueScheduler\Options;

use GoalioQueueDoctrine\Options\DoctrineOptions;

class SchedulerOptions extends DoctrineOptions
{
    protected $scheduleAhead;

    public function setScheduleAhead($scheduleAhead)
    {
        $this->scheduleAhead = $scheduleAhead;
    }

    public function getScheduleAhead()
    {
        return $this->scheduleAhead;
    }

}