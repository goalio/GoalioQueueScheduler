<?php
namespace GoalioQueueScheduler\Job;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Job\JobInterface;

abstract class AbstractScheduledJob extends AbstractJob implements ScheduledJobInterface {

    protected $queue;

    public function __construct(JobInterface $job = null, array $options = array()) {
        $this->content = array(
            'job' => $job,
            'options' => $options,
            'handles' => array(),
        );
    }

    public function setQueue(QueueInterface $queue) {
        $this->queue = $queue;
    }

    public function getQueue() {
        if($this->queue === null) {
            throw new \Exception("Queue must be set");
        }
        return $this->queue;
    }

    public function getJob() {
        return $this->content['job'];
    }

    public function setJob(JobInterface $job) {
        $this->content['job'] = $job;
        return $this;
    }

    public function getHandles() {
        $handles = array();
        foreach($this->content['handles'] as $timestamp => $id) {
            $handles[$timestamp] = new JobHandle($id);
        }
        return $handles;
    }

    public function getOptions() {
        return array();
    }

    public function getContent() {
        $content = $this->content;
        if($content['job'] instanceof JobInterface) {
           $content['job'] = $content['job']->jsonSerialize();
        }
        return $content;
    }

    protected function getJobOptions() {
        return $this->content['options'];
    }




}