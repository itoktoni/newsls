<?php

namespace App\Listeners;

use App\Dao\Models\User;
use App\Events\CreateScheduleEvent;
use App\Mail\CreateScheduleEmail;
use Illuminate\Support\Facades\Mail;

class CreateScheduleListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(CreateScheduleEvent $event)
    {
        $report_from = 'admin@demo.com' ?? false;
        $report_to = auth()->user()->{User::field_email()} ?? false;

        if ($report_to) {
            Mail::to([$report_from, $report_to])->send(new CreateScheduleEmail($event->data));
        }
    }
}
