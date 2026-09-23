<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreateMovementEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;

    public $product_name;

    public $location_old;

    public $location_new;

    public function __construct($data, $product_name, $location_old, $location_new)
    {
        $this->data = $data;
        $this->product_name = $product_name;
        $this->location_old = $location_old;
        $this->location_new = $location_new;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.create_movement', ['data' => $this->data, 'product_name' => $this->product_name, 'location_old' => $this->location_old, 'location_new' => $this->location_new]);
    }
}
