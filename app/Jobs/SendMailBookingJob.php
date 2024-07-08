<?php

namespace App\Jobs;

use App\Mail\SendMailBooking;
use App\Mail\SendMailBookingCancel;
use App\Mail\SendMailBookingCancelAdmin;
use App\Mail\SendMailBookingComplete;
use App\Mail\SendMailBookingConfirm;
use App\Models\Admin;
use App\Models\Booking;
use App\Notifications\NewTourNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Pusher\Pusher;

class SendMailBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;
    protected $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, $status = 0)
    {
        $this->booking = $booking;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $status = !empty($this->status) ? $this->status : $this->booking->status;

        switch ($status) {
            case BOOKING_NEW:
                $dataNotification = [
                    'content' => 'KH ' . $this->booking->customer->name . ' vừa booking tour ' . $this->booking->tour->name,
                    'url' => route('bookings.show', $this->booking->id),
                ];
                Admin::find(1)->notify(new NewTourNotification($dataNotification));
                $options = array(
                    'cluster' => 'ap1',
                    'encrypted' => true
                );

                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    $options
                );

                $pusher->trigger('NotificationEvent', 'send-message', [
                    'content' => 'KH ' . $this->booking->customer->name . ' vừa booking tour ' . $this->booking->tour->name,
                    'url' => route('bookings.show', $this->booking->id),
                ]);
                $email = new SendMailBooking($this->booking);
                Mail::to(config('config.email'))->send($email);
                break;
            case BOOKING_CONFIRM:
                $email = new SendMailBookingConfirm($this->booking);
                Mail::to($this->booking->customer->email)->send($email);
                break;
            case BOOKING_COMPLETE:
                $email = new SendMailBookingComplete($this->booking);
                Mail::to($this->booking->customer->email)->send($email);
                break;
            case BOOKING_CANCEL:
                $email = new SendMailBookingCancel($this->booking);
                Mail::to($this->booking->customer->email)->send($email);
                break;
            case BOOKING_CANCEL_PROCESSING:
                $emailAdmin = new SendMailBookingCancelAdmin($this->booking);
                Mail::to(config('config.email'))->send($emailAdmin);
                break;
            default:
                break;
        }
    }
}
