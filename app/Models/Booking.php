<?php

namespace App\Models;

use App\Jobs\SendMailBookingJob;
use App\Libraries\Utilities;
use App\Libraries\VNPayPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $dates = ['deleted_at'];
    /**
     * Get the tour that owns the booking.
     *
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the customer that owns the booking.
     *
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, BookingRoom::class)
            ->withPivot(['id', 'price', 'number']);
    }

    public function booking_room()
    {
        return $this->hasMany(BookingRoom::class);
    }

    /**
     * Validate rules for booking
     *
     * @return array[]
     */
    public function rule(): array
    {
        return ['status' => 'required|integer|between:1,3'];
    }

    /**
     * Update status for booking
     *
     * @param Request $request
     * @param $id
     * @return bool
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = $this->findOrFail($id);
        $diffStatus = $request->status - $booking->status;
        $booking->status = $request->status;

        if ($diffStatus != 1 && $request->status != BOOKING_CANCEL) {
            return false;
        }

        if ($request->status == BOOKING_CANCEL) {
            $diffDay = Carbon::parse($booking->departure_time)->diffInDays(now());
            $refund = $diffDay > 3 ? $booking->deposit : $booking->deposit * 0.8;
            $booking->refund = $refund;
            $response = VNPayPayment::refund([
               'orderId' => $booking->invoice_no,
               'amountRefund' => $refund,
               'tranNo' => $booking->transaction_id,
               'tranDate' => $booking->created_at,
               'userName' => $booking->customer->name,
            ]);

            if (!$response->successful()) {
                return false;
            }
        }

        dispatch(new SendMailBookingJob($booking, $request->status));
        return $booking->save();
    }

    /**
     * Get a list of destinations
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getList(Request $request)
    {
        $search = $request->search;
        $status = $request->status ?? BOOKING_NEW;

        $query = $this->with('tour.type', 'tour.destination', 'customer');

        if (!empty($search)) {
            $search = Utilities::clearXSS($search);
            $query->whereHas('customer', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orwhere('email', 'like', '%' . $search . '%');
                    $query->orwhere('phone', 'like', '%' . $search . '%');
                    $query->orWhere(DB::raw('concat(first_name," ",last_name)'), 'like',
                        '%' . $search . '%');
                });
            });

            $query->orWhereHas('tour', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($status)) {
            if ($status == BOOKING_NEW) {
                $query->whereDate('departure_time', '>', now())->where('status', BOOKING_NEW);
            } else if ($status == BOOKING_OVER_DATE) {
                $query->whereDate('departure_time', '<=', now())->where('status', BOOKING_NEW);
            } else {
                $query->where('status', $status);
            }
        }

        $data = $query->orderBy('departure_time')->orderBy('created_at')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('tour.name', function ($data) {
                $name = $data->tour->name;
                $destination = $data->tour->destination->name;
                $type = $data->tour->type->name;
                $duration = Utilities::durationToString($data->tour->duration);

                return view('components.title_tour', compact(['name', 'destination', 'type', 'duration']));
            })
            ->editColumn('customer_name', function ($data) {
                return $data->customer->first_name . ' ' . $data->customer->last_name;
            })
            ->editColumn('price', function ($data) {
                return number_format($data->price) . ' đ';
            })
            ->editColumn('payment_method', function ($data) {
                switch ($data->payment_method) {
                    case 1:
                        return 'Tiền mặt';
                    case 2:
                        return 'VnPay';
                    default:
                        return null;
                }
            })
            ->editColumn('status', function ($data) {
                return view('components.status_booking', ['status' => $data->status]);
            })
            ->editColumn('departure_time', function ($data) {
                return date('d/m/Y', strtotime($data->departure_time)) . ' ~ ' . Carbon::parse($data->departure_time)->addDays($data->tour->duration)->format('d/m/Y');
            })
            ->editColumn('countdown', function ($data) {
                $day = $data->departure_time > now() ? Carbon::parse($data->departure_time)->diffInDays(now()) : 0;
                return view('components.countdown_booking', ['day' => $day]);
            })
            ->addColumn('total', function ($data) {
                return number_format($data->total) . ' đ';
            })
            ->addColumn('action', function ($data) {
                $link = route('bookings.show', $data->id);
                $width = 80;

                return view('components.button_link_info', [
                    'link' => $link,
                    'title' => 'Chi tiết',
                    'width' => $width,
                ]);
            })
            ->make(true);
    }

    public function getRevenue($start, $end)
    {
        $startDate = new Carbon($start);
        $endDate = (new Carbon($end))->addDay();
        $arrDates = Utilities::dateRange($start, $end);
        $numberDates = count($arrDates);

        $success = array_fill(0, $numberDates, 0);
        $reject = array_fill(0, $numberDates, 0);
        $other = array_fill(0, $numberDates, 0);

        $bookings = $this->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<', $endDate)
            ->select('total', 'status', 'created_at')
            ->get();

        foreach ($bookings as $booking) {
            $i = array_search($booking->created_at->format('Y-m-d'), $arrDates);
            if ($i > -1) {
                switch ($booking->status) {
                    case BOOKING_COMPLETE:
                        $success[$i] += $booking->total;
                        break;
                    case BOOKING_CANCEL:
                        $reject[$i] += $booking->total;
                        break;
                    case BOOKING_NEW:
                        $other[$i] += $booking->total;
                        break;
                }
            }
        }

        return [
            'date' => $arrDates,
            'success' => $success,
            'reject' => $reject,
            'other' => $other,
        ];
    }
}
