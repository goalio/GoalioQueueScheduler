<?php
namespace GoalioQueueScheduler\Queue;

use GoalioQueueScheduler\Job\Cronjob;
use GoalioQueueScheduler\Job\ScheduledJob;
use GoalioQueueScheduler\Job\ScheduledJobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Job\JobPluginManager;
use Doctrine\DBAL\Connection;
use GoalioQueueDoctrine\Queue\Table;
use SlmQueue\Job\JobInterface;

class Schedule extends Table {

    /**
     * how long should jobs be scheduled in the future (in minutes)
     * @var int
     */
    protected $scheduleAhead;

    /**
     * wrapped queue
     * @var \SlmQueue\Queue\QueueInterface
     */
    protected $queue;


    public function __construct(QueueInterface $queue, Connection $connection, $tableName, $name, JobPluginManager $jobPluginManager) {
        $this->queue = $queue;
        parent::__construct($connection, $tableName, $name, $jobPluginManager);
    }

    /**
     * how long should jobs be scheduled in the future (in minutes)
     * @param int $scheduleAhead
     */
    public function setScheduleAhead($scheduleAhead)
    {
        $this->scheduleAhead = $scheduleAhead;
    }

    /**
     * @return int
     */
    public function getScheduleAhead()
    {
        return $this->scheduleAhead;
    }

    /**
     * Push a new job into the queue
     *
     * @param  JobInterface $job
     * @param  array        $options
     * @return void
     */
    public function push(JobInterface $job, array $options = array()) {
        if(isset($options['frequency'])) {
            $frequency = $options['frequency'];
            unset($options['frequency']);
            $cronjob = new Cronjob($job, $options, $frequency);
            parent::push($cronjob, $cronjob->getOptions());
        }
        elseif(isset($options['scheduled'])) {
            $scheduled = $options['scheduled'];
            unset($options['scheduled']);
            $scheduledjob = new ScheduledJob($job, $options, $scheduled);
            parent::push($scheduledjob, $scheduledjob->getOptions());
        }
        else {
            $this->queue->push($job, $options);
        }
    }

    /**
     * Delete a job from the queue
     *
     * @param  JobInterface $job
     * @return void
     */
    public function delete(JobInterface $job) {
         if($job instanceof ScheduledJobInterface) {
              foreach($job->getHandles() as $handle) {
                 $this->queue->delete($handle);
             }
        }
        parent::delete($job);
    }

    protected function createJob($className, $content = null, array $metadata = array())
    {
        $job = parent::createJob($className, $content, $metadata);
        if($job instanceof ScheduledJobInterface) {
            $data = json_decode($content['job'], true);
            $job->setJob($this->createJob($data['class'], $data['content']));
            $job->setQueue($this->queue);
        }
        return $job;
    }



}