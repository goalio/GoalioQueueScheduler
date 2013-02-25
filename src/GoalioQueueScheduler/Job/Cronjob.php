<?php
namespace GoalioQueueScheduler\Job;

use Heartsentwined\CronExprParser\Parser;
use SlmQueue\Job\JobInterface;
use GoalioQueueDoctrine\Job\Exception;

class Cronjob extends AbstractScheduledJob {

    protected $scheduleAhead;
    protected $parser;

    public function __construct(JobInterface $job = null, array $options = array(), $frequency = null, $scheduleAhead = 60) {
        parent::__construct($job, $options);
        $this->scheduleAhead = $scheduleAhead;
        $this->content['frequency'] = $frequency;
    }

    /**
     * @param \GoalioQueue\Scheduler\Parser $parser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return \GoalioQueue\Scheduler\Parser
     */
    public function getParser()
    {
        if($this->parser === null) {
            $this->parser = new Parser();
        }
        return $this->parser;
    }


    protected function getFrequency() {
        return $this->content['frequency'];
    }


    /**
     * Execute the job
     *
     * @return void
     */
    public function execute()
    {
        // Deal with the same time everywhere
        $now = time();

        // Timeframe in which upcoming jobs are pushed
        // as well as the time for the next run
        $timeAhead =  $now + ($this->scheduleAhead * 60);

        $handles = &$this->content['handles'];

        // Iterate over the minutes in the scheduleAhead timeframe
        for($time = $now; $time < $timeAhead; $time += 60) {

            // Normalize time to hours + minutes
            $scheduleTime = new \DateTime();
            $scheduleTime->setTimestamp($time);
            $scheduleTime->setTime(
                $scheduleTime->format('H'),
                $scheduleTime->format('i')
            );
            $scheduled = $scheduleTime->getTimestamp();


            // Skip already scheduled jobs
            if(isset($handles[$scheduled])) {
                continue;
            }

            // Check if the scheduled timestamp
            // (which is in minute steps, in the  scheduleAhead timeframe)
            // is a time which matches the cron frequency
            if($this->getParser()->matchTime($scheduled, $this->getFrequency())) {
                $now2    = time();

                $job     = $this->getJob();
                $options = $this->getJobOptions();

                // Queue specific: Add delay
                if($scheduled > $now2) {
                    $options['delay'] = $scheduled - $now2;
                }

                // Finally push it to the queue
                $this->getQueue()->push($job, $options);

                $handles[$scheduled] = $job->getId();
            }
        }

        throw new Exception\ReleasableException(array('scheduled' => $timeAhead));
    }

}