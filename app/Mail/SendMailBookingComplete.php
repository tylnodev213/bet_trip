<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SendMailBookingComplete extends Mailable
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
        $token = sprintf('booking_id=%s&customer_id=%s', $this->booking->id, $this->booking->customer->id);
        $link = route('client.tours.detail', $this->booking->tour->slug) . '?token=' . encrypt($token);
        $qrCode = QrCode::format('png')->size(300)->generate($link);
        $qrCodePath = 'qrcodes/' . uniqid() . '.png';
        Storage::disk('public')->put($qrCodePath, $qrCode);
        return $this->subject("Thư cảm ơn - GoodTrip")
            ->view('mails.booking_complete', ['link' => $link])->with('booking', $this->booking)->attach(Storage::disk('public')->url($qrCodePath), [
                'as' => 'qrcode.png',
                'mime' => 'image/png',
            ]);
    }
}
