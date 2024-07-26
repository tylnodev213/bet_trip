<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteTour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:complete_tour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command update status booking to complete';

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
        $this->info('==========Start update status booking to complete===============');
        $bookings = Booking::where('status', BOOKING_CONFIRM)->where('departure_time', '<=', now()->ceilDay(7))->get();
        foreach ($bookings as $booking) {
            $duration = $booking->tour->duration;
            if (Carbon::parse($booking->departure_time)->addDays($duration)->format('Y-m-d') != date('Y-m-d')) {
                continue;
            }
            if (!Booking::where('id', $booking->id)->update('status', BOOKING_COMPLETE)) {
                $this->error("==========Update status booking {$booking->id} fail!===============");
                continue;
            }
            $this->info("==========Update status booking {$booking->id} successful!===============");
        }
        $this->info('==========End update status booking to complete===============');

        return 0;
    }
}
