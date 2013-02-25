<?php
namespace GoalioQueueScheduler\Job;

class ScheduledJob extends AbstractScheduledJob {

    public function execute() {
        // Psuh job  to the queue
        $this->getQueue()->push($this->getJob(), $this->getJobOptions());
    }

}