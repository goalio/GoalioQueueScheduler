GoalioQueueDoctrine
===================

Version 0.2.0 Created by Stefan Kleff

Since most of available queues do not support recurring jobs, as well as running jobs at a scheduled point in time,
this module aims to provide these feature in a transparent way on top of the existing queues.

Requirements
------------
* [Zend Framework 2](https://github.com/zendframework/zf2)
* [SlmQueue](https://github.com/juriansluiman/SlmQueue)
* [Doctrine 2 ORM Module](https://github.com/doctrine/DoctrineORMModule)
* [Goalio Queue Doctrine](https://github.com/goalio/GoalioQueueDoctrine)
* [Cron Expression Parser](https://github.com/heartsentwined/php-cron-expr-parser)


Installation
------------

First, install GoalioQueueDoctrine ([instructions here](https://github.com/goalio/GoalioQueueDoctrine/blob/master/README.md)). Then,
add the following line into your `composer.json` file:

```json
"require": {
	"goalio/goalio-queue-scheduler": ">=0.2"
}
```

Then, enable the module by adding `GoalioQueueScheduler` in your application.config.php file. You may also want to
configure the module: just copy the `goalio_queue_scheduler.local.php.dist` (you can find this file in the config
folder of GoalioQueueScheduler) into your config/autoload folder, and override what you want.

The installation is almost like the default GoalioQueueDoctrine module.


Documentation
-------------

This module adds two features to any queue supported by [SlmQueue](https://github.com/juriansluiman/SlmQueue),
which is able to handle delayed jobs. (F.ex. [Beanstalkd](https://github.com/juriansluiman/SlmQueueBeanstalkd) and [Doctrine](https://github.com/goalio/GoalioQueueDoctrine))

1. You can define a crontab-like execution frequency supported by [Cron Expression Parser](https://github.com/heartsentwined/php-cron-expr-parser)
2. You can add a fixed date in the future when the job should be executed

This is achieved by adding another queue(A) as decorator of your regular queue(B) which supports the options 'scheduled' and 'frequency'.
If one of your jobs has one of these options the job will be added to queue(A).
A periodically running worker on queue(A) schedules the job in queue(B) if the crontab frequency matches or the 'scheduled' point in time has come.

A rather simple algorithm is used to schedule the jobs:
The crontab-job iterates over the next n minutes and checks for every minute in the timeframe if this time matches the crontab frequency.
This approach is based on [ZF2 Cron](https://github.com/heartsentwined/zf2-cron).





### Setting the connection parameters

Copy the `goalio_queue_doctrine.local.php.dist` file to your `config/autoload` folder, and follow the instructions.


### Adding queues
For adding scheduling functionality to your queue, you simply need to add a decorator to your original queue.
Keep in mind that you have to run workers on the schedule queue to add scheduled jobs to you original queue.

```php
return array(
 'slm_queue' => array(
     'queues' => array(
         'factories' => array(
             'schedule' => new \GoalioQueueScheduler\Factory\ScheduleFactory('original_queue_name'),
         )
     )
 )
);
 ```

### Executing / Scheduling jobs

GoalioQueueScheduler does not provide a command-line tool, but you can use the default worker
from the GoalioQueueDoctrine module to process the queue.