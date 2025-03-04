<?php

namespace App\Services;

use App\Jobs\SendMailBookingJob;
use App\Libraries\Utilities;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Destination;
use App\Models\Followers;
use App\Models\Room;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;


class ClientService
{
    /**
     * Rule for store new booking tour
     *
     * @return string[]
     */
    public function ruleBooking(): array
    {
        return [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|regex:/^(0)[0-9]{9,10}$/',
            'number_adults' => 'required|integer|min:0|max:20',
            'number_children' => 'nullable|integer|min:0|max:20',
            'departure_time' => 'required|date|after:' . date('Y-m-d', strtotime("+ 1day")),
            'payment_method' => 'required|integer|min:0|max:3',
            'address' => 'string|max:100|required',
            'city' => 'string|max:50|required',
            'province' => 'string|max:50|required',
            'country' => 'string|max:25|required',
            'identification' => 'nullable|string',
            'followers.*.name' => 'nullable|required_with:followers.*.age,followers.*.identification,followers.*.relationship',
            'followers.*.age' => 'nullable|required_with:followers.*.name,followers.*.identification,followers.*.relationship',
            'followers.*.identification' => 'nullable',
            'followers.*.relationship' => 'nullable|required_with:followers.*.age,followers.*.name,followers.*.identification',
        ];
    }

    /**
     * Store booking when user book tour
     *
     * @param Request $request
     * @param $tour
     * @return Booking
     */
    public function storeBooking(Request $request, $tour)
    {
        $input = Utilities::clearAllXSS($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'address',
            'city',
            'province',
            'country',
            'identification',
        ]));
        $input['status'] = 1;
        $customer = Customer::create($input);
        $customerId = $customer->id;
        Cookie::queue('customer_id', $customerId, 10080);
        $input = $request->only([
            'followers',
        ]);
        $input = array_map(function ($item) use ($customerId) {
            if (!empty($item['name']) && !empty($item['age']) && !empty($item['relationship'])) {
                return [
                    'customer_id' => $customerId,
                    'name' => $item['name'] ?? '',
                    'age' => $item['age'] ?? '',
                    'identification' => $item['identification'] ?? '',
                    'relationship' => $item['relationship'] ?? '',
                ];
            } else {
                return [];
            }
        }, $input['followers']);
        $followers = Followers::insert(array_filter($input));
        $rooms = Room::where('tour_id', $tour->id)->get();
        $coupon = Coupon::where('code', $request->codeCoupon)->first();

        $dataRoom = [];
        $totalPriceRoom = 0;
        foreach ($rooms as $room) {
            $roomInsert = new BookingRoom();
            $roomInsert->room_id = $room->id;
            $roomInsert->number = 0;
            $roomInsert->price = $room->price;

            foreach ($request->room as $roomRequest) {
                if ($roomRequest['id'] == $room->id && $roomRequest['number'] > 0) {
                    $roomInsert->number = $roomRequest['number'];
                }
            }

            $totalPriceRoom += $roomInsert->number * $roomInsert->price;

            $dataRoom[] = $roomInsert;
        }

        $input = Utilities::clearAllXSS($request->only([
            'number_adults',
            'number_children',
            'payment_method',
            'departure_time',
            'requirement',
        ]));
        $input['customer_id'] = $customerId;
        $input['tour_id'] = $tour->id;
        $input['discount_code'] = @$coupon->code;
        $input['discount'] = @$coupon->discount ?? 0;

        $total = $tour->price_adult * $request->number_adults + $tour->price_child * $request->number_children + $totalPriceRoom;
        $total = $total - $total * $input['discount'] / 100;
        $input['total'] = $total;
        $input['status'] = 1;

        if ($request->booking_id) {
            $booking = Booking::find($request->booking_id);
            $booking->update($input);
        } else {
            $booking = Booking::create($input);
            $booking->booking_room()->saveMany($dataRoom);
            if ($coupon) {
                $coupon->update([
                    'number' => $coupon->number - 1,
                ]);
            }
        }

        return $booking;
    }

    /**
     * get filter
     *
     * @param Request $request
     * @param $query
     * @return mixed
     */
    public function filterTour(Request $request, $query)
    {
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;
        $filterDuration = $request->filter_duration;
        $filterType = $request->filter_type;

        if (is_numeric($minPrice) && is_numeric($maxPrice)) {
            $query->where('price_child', '>=', $minPrice)
                ->where('price_adult', '<=', $maxPrice);
        }

        if (!empty($filterDuration)) {
            $query->where(function ($query) use ($filterDuration) {
                foreach ($filterDuration as $filter) {
                    if ($filter == 1) {
                        $query->whereBetween('duration', [0, 3]);
                    }

                    if ($filter == 2) {
                        $query->orwhereBetween('duration', [3, 5]);
                    }

                    if ($filter == 3) {
                        $query->orwhereBetween('duration', [5, 7]);
                    }

                    if ($filter == 4) {
                        $query->orWhere('duration', '>', 7);
                    }
                }
            });
        }

        if (!empty($filterType)) {
            $query->whereIn('type_id', $filterType);
        }

        return $query;
    }

    /**
     * Get list tour with filter
     *
     * @param Request $request
     * @param $slug
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getListTour(Request $request, $slug)
    {
        $specialSlug = ['all', 'new', 'trending'];
        $query = Tour::with('destination', 'type')
            ->where('status', ACTIVE)
            ->latest();

        if (!in_array($slug, $specialSlug)) {
            $destination = Destination::where('slug', $slug)->firstOrFail();
            $query->where('destination_id', $destination->id);
        }

        if ($slug == 'trending') {
            $query->where('trending', ACTIVE);
        }

        $query = $this->filterTour($request, $query);
        $tours = $query->paginate(9);
        $tours->appends(request()->query());

        return $tours;
    }

    /**
     * search tour
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchTour($request)
    {
        $query = Tour::select(['tours.*', DB::raw("(SELECT COUNT(*) FROM bookings WHERE tour_id = tours.id AND status <> 4 AND deleted_at IS NULL) as booking_count")])
            ->with('destination', 'type')
            ->where('status', 1);

        $tourName = $request->tour_name;
        $destinationName = $request->destination_name;
        $duration = $request->duration;

        if (!empty($tourName)) {
            $query->where('name', 'like', '%' . $tourName . '%');
        }

        if (!empty($destinationName)) {
            $query->whereHas('destination', function ($query) use ($destinationName) {
                $query->where('name', 'like', '%' . $destinationName . '%');
            });
        }

        if (!empty($duration)) {
            $query->where('duration', $duration);
        }

        $query = $this->filterTour($request, $query);

        return $query->orderBy('booking_count', 'desc')->paginate(21);
    }

    /**
     * Get list destination
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listDestination()
    {
        return Destination::latest()->paginate(12);
    }
}
