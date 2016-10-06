<?php

namespace App\Listeners;

use App\Events\LogEvent;
use App\Models\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LogEvent  $event
     * @return void
     */
    public function handle(LogEvent $event)
    {
        $user_id = null;
        if(!empty($event->user) && !empty($event->user['id'])) {
            $user_id = $event->user['id'];
        }

        $log = new Log();
        $log['ip'] = $event->ip;
        $log['user_id'] = $user_id;
        $log['type'] = $event->type;
        $log['at'] = $event->time;
        $log['message'] = $event->message;
        $log->save();
    }
}
