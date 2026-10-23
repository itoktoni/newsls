<?php

namespace App\Listeners;

use App\Dao\Enums\NotificationStatus;
use App\Dao\Models\Notification;
use App\Dao\Models\User;
use App\Events\CreateWorkSheetEvent;
use App\Mail\CreateWorkSheetEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CreateWorkSheetListener
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
    public function handle(CreateWorkSheetEvent $event)
    {
        $report_from = auth()->user()->{User::field_email()} ?? false;
        $report_to = $event->data->has_reported_by->field_email ?? false;
        $type = $event->data->has_work_type->field_name ?? '';

        // if ($report_to) {
        //     Mail::to([$report_from, $report_to])->send(new CreateWorkSheetEmail($event->data, $type));
        // }
    }

    private function saveNotification($pelapor, $description, $phone, $type, $picture = null)
    {

        Notification::create([
            Notification::field_name() => $pelapor,
            Notification::field_description() => $description,
            Notification::field_status() => NotificationStatus::Create,
            Notification::field_phone() => $phone,
            Notification::field_eta() => Carbon::today(),
            Notification::field_type() => $type,
            Notification::field_image() => $picture ? asset('storage/ticket/'.$picture) : null,
        ]);
    }
}
