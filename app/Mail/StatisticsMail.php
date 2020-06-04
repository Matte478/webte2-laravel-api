<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatisticsMail extends Mailable
{
    use Queueable, SerializesModels;

    private $statistics;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($statistics)
    {
        $this->statistics = $statistics;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.statistics')
            ->with(['statistics' => $this->statistics]);
    }
}
