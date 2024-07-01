<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailBookingConfirm extends Mailable
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
        return $this->subject("Tour " . $this->booking->tour->name . " được xác nhận - GoodTrip")
            ->view('mails.booking_confirm', ['link' => $link])->with('booking', $this->booking);
    }
}
