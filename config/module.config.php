<?php
return array(

    'service_manager' => array(
        'factories' => array(
            'GoalioQueueScheduler\Options\SchedulerOptions' => 'GoalioQueueScheduler\Factory\SchedulerOptionsFactory',
        )
    ),

    'slm_queue' => array(
        'scheduler' => array(
            'connection' => 'doctrine.connection.orm_default',
            'table_name' => 'queue_schedule',
            'deleted_lifetime' => '60',
            'buried_lifetime' => '60',
            'schedule_ahead' => '60',
        ),
    ),
);
