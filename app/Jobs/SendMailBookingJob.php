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
use Illuminate\Support\Facades\Auth;
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
                if (!Auth::check()) {
                    $emailAdmin = new SendMailBookingCancelAdmin($this->booking);
                    Mail::to(config('config.email'))->send($emailAdmin);
                }
                $email = new SendMailBookingCancel($this->booking);
                Mail::to($this->booking->customer->email)->send($email);
                break;
            default:
                break;
        }
    }
}
