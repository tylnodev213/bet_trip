<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Refund;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.bookings.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $booking = Booking::findOrFail($id);

        if (!empty($noticeId = request('notification_id'))) {
            Auth::user()->unreadNotifications->where('id', $noticeId)->markAsRead();
        }

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Change status booking
     *
     * @param int $id
     * @return string
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate($this->booking->rule());
        if ($request->status == BOOKING_COMPLETE) {
            $booking = Booking::find($id);
            $duration = $booking->tour->duration;
            $endDate = Carbon::parse($booking->departure_time)->addDays($duration)->format('Y-m-d');
            if ($endDate > date('Y-m-d')) {
                return json_encode(['status' => false]);
            }
        }
        return json_encode($this->booking->updateStatus($request, $id));
    }

    public function updateDeposit(Request $request, $id)
    {
        $request->validate([
            'deposit' => 'required|integer'
        ]);
        $booking = Booking::findOrFail($id);

        return json_encode($booking->update([
            'deposit' => $request->deposit,
        ]));
    }

    public function updateRefund(Request $request, $id)
    {
        $request->validate([
            'refund' => 'required|integer'
        ]);
        $booking = Booking::findOrFail($id);

        return json_encode($booking->update([
            'refund' => $request->refund,
        ]));
    }

    public function update(Request $request, $id)
    {
        $totalPrice = 0;
        $booking = Booking::findOrFail($id);
        $totalPrice += $booking->tour->price_adult * $request->number_adults;
        $totalPrice += $booking->tour->price_child * $request->number_children;

        if (!empty($request->room)) {
            foreach ($request->room as $key => $value) {
                $bookingRoom = BookingRoom::find($key);
                $bookingRoom->update([
                    'number' => $value,
                ]);
                $bookingRoom->save();
                $totalPrice += $bookingRoom->price * $bookingRoom->number;
            }
        }

        $totalPrice = $totalPrice - ($totalPrice * $booking->discount / 100);
        $booking->update([
            'number_adults' => $request->number_adults,
            'number_children' => $request->number_children,
            'total' => $totalPrice,
        ]);

        return redirect()->route('bookings.show', $booking->id);
    }


    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            return $this->booking->getList($request);
        }
    }

    public function getChartData(Request $request)
    {
        $data = $this->booking->getRevenue($request->startDate, $request->endDate);
        return response()->json([
            'booking' => $data
        ]);
    }

    public function downloadInvoice($id)
    {
        $booking = Booking::findOrFail($id);
        $qrCode = QrCode::format('png')->size(150)->generate(route('bookings.invoice', ['id' => $booking->id]));
        return view('admin.bookings.invoice', compact('booking', 'qrCode'));
    }
}
