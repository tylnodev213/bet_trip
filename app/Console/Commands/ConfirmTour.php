<?php

namespace App\Console\Commands;

use App\Mail\SendMailBookingConfirm;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ConfirmTour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:confirm_tour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command update status booking to confirm';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('==========Start update status booking to confirm===============');
        $bookings = Booking::where('status', BOOKING_NEW)->where('departure_time', date('Y-m-d', strtotime('-1 day')))->get();
        foreach ($bookings as $booking) {
            $booking->status = BOOKING_CONFIRM;
            $booking->save();
            $email = new SendMailBookingConfirm($booking);
            Mail::to($booking->customer->email)->send($email);
            $this->info("==========Update status booking {$booking->id} successful!===============");
        }
        $this->info('==========End update status booking to confirm===============');

        return 0;
    }
}
