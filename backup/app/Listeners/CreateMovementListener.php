<?php

namespace App\Listeners;

use App\Dao\Models\Location;
use App\Dao\Models\Product;
use App\Dao\Models\User;
use App\Events\CreateMovementEvent;
use App\Mail\CreateMovementEmail;
use Illuminate\Support\Facades\Mail;

class CreateMovementListener
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
    public function handle(CreateMovementEvent $event)
    {
        $report_from = auth()->user()->{User::field_email()} ?? false;
        $report_to = $event->data->has_user->{User::field_email()} ?? false;
        $product_name = $event->data->has_product->{Product::field_name()} ?? '-';
        $location_old = $event->data->has_location_old->{Location::field_name()} ?? '-';
        $location_new = $event->data->has_location->{Location::field_name()} ?? '-';

        if ($report_to) {
            Mail::to([$report_from, $report_to])->send(new CreateMovementEmail($event->data, $product_name, $location_old, $location_new));
        }
    }
}
