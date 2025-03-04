<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailBookingCancel extends Mailable
{
    use Queueable, SerializesModels;

    protected $booking;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $token = $this->booking->id;
        $link = route('order', ['token' => encrypt($token)]);
        return $this->subject("Hủy đặt tour " . $this->booking->tour->name . " - GoodTrip")
            ->view('mails.booking_cancel', ['link' => $link])->with('booking', $this->booking);
    }
}
