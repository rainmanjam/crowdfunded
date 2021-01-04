<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PledgeConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The pledge instance.
     *
     * @var Pledge
     */
    public $pledge;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pledge)
    {
        $this->pledge = $pledge;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.pledge-confirmation');
    }
}
