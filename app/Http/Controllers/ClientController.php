<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailBookingJob;
use App\Libraries\Notification;
use App\Libraries\Utilities;
use App\Libraries\VNPayPayment;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Destination;
use App\Models\Review;
use App\Models\Tour;
use App\Models\Type;
use App\Notifications\NewTourNotification;
use App\Services\ClientService;
use App\Services\NotifyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClientController extends Controller
{
    protected $notification;
    protected $clientService;

    public function __construct(Notification $notification, ClientService $clientService)
    {
        $this->notification = $notification;
        $this->clientService = $clientService;
    }

    /**
     * Display a Homepage.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Destination $destination, Type $type, Tour $tour, Coupon $coupon)
    {
        $destinations = $destination->getByStatus(1, 5);
        $types = $type->getByStatus(1, 3);
        $trendingTours = $tour->getByTrending(true, 3);
        $tours = $tour->getByGuide(1, 6);
        $coupons = $coupon->getByStatus(1, 5);

        return view('index', compact(['destinations', 'trendingTours', 'types', 'tours', 'coupons']));
    }

    /**
     * Show list tour of destination.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function listTour(Request $request, $slug, Type $type)
    {
        $types = $type->getOrderByTitle();
        $tours = $this->clientService->getListTour($request, $slug);
        $filterDuration = $request->filter_duration ?? [];
        $filterType = $request->filter_type ?? [];
        $destination = Destination::where('slug', $slug)->first();

        return view('list_tour', compact(['tours', 'types', 'filterDuration', 'filterType', 'destination']));
    }

    /**
     * Show tour detail.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function showTour(Request $request, $slug, Tour $tourModel)
    {
        $tour = $tourModel->getTourBySlug($slug);
        $tour->faqs = $tour->faqs(true)->get();
        $tour->reviews = $tour->reviews(true)->get();
        $relateTours = $tourModel->getRelated($tour);
        $reviews = $tour->reviews(true)->paginate(8);
        $rateReview = Utilities::calculatorRateReView($tour->reviews);
        $request->merge([
            'departure_time' => date('Y-m-d'),
        ]);
        $roomAvailable = $this->checkRoom($request, $slug)->getContent();

        $enableComment = true;
        $customer = null;
        try {
            $token = $request->token;
            $bookingId = decrypt($token);
            $booking = Booking::where('id', $bookingId)->where('tour_id', $tour->id)->where('status', BOOKING_COMPLETE)->first();
            if (empty($booking) || $booking->tour->reviews->count() >= $tour->bookings->count()) {
                $enableComment = false;
            }
            $customer = !empty($booking) ? $booking->customer : null;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            $enableComment = false;
        }

        return view('tour_detail', compact(['tour', 'relateTours', 'reviews', 'rateReview', 'enableComment', 'customer', 'roomAvailable']));
    }

    /**
     * Show booking page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function booking(Request $request, $slug, Tour $tourModel)
    {
        $customerId = Cookie::get('customer_id');
        $customer = null;
        if (!empty($customerId)) {
            $customer = Customer::find($customerId);
        }
        $tour = $tourModel->getTourBySlug($slug);
        $numberAdults = $request->number_adults;
        $numberChildren = $request->number_children;
        $departureTime = $request->departure_time;
        $listRooms = $request->room;
        $booking = null;
        $roomAvailable = $this->checkRoom($request, $slug)->getContent();

        return view('booking', compact(['tour', 'numberChildren', 'numberAdults', 'departureTime', 'listRooms', 'booking', 'roomAvailable', 'customer']));
    }

    /**
     * Display contact page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Store contact
     *
     * @param Request $request
     * @param Contact $contact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeContact(Request $request, Contact $contact)
    {
        $request->validate($contact->rules(), [], [
            'name' => 'tên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'message' => 'nội dung',
        ]);
        try {
            $contact->saveData($request);
            $this->notification->setMessage('Gửi phản hồi thành công', Notification::SUCCESS);

            return redirect()->route('index')->with($this->notification->getMessage());
        } catch (Exception $e) {
            $this->notification->setMessage('Gửi phản hồi thất bại', Notification::ERROR);
            Log::error($e->getMessage());
            return back()
                ->with('exception', $e->getMessage())
                ->with($this->notification->getMessage())
                ->withInput();
        }
    }

    /**
     * Display search page.
     *
     * @param Request $request
     * @param Type $type
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function search(Request $request, Type $type)
    {
        $types = $type->getOrderByTitle();
        $tours = $this->clientService->searchTour($request);
        $filterDuration = $request->filter_duration ?? [];
        $filterType = $request->filter_type ?? [];

        return view('search', compact(['tours', 'types', 'filterDuration', 'filterType']));
    }

    /**
     * Display destination page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function destination()
    {
        $destinations = $this->clientService->listDestination();

        return view('destination', compact(['destinations']));
    }

    /**
     * Store review
     *
     * @param Request $request
     * @param $slug
     * @param Review $review
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReview(Request $request, $slug, Review $review)
    {
        $request->validate($review->rules());
        try {
            $tour = Tour::where('slug', $slug)->firstOrFail();
            $review->saveData($request, $tour);
            $this->notification->setMessage('Đánh giá đã được gửi thành công', Notification::SUCCESS);

            return back()->with($this->notification->getMessage());
        } catch (Exception $e) {
            $this->notification->setMessage('Đánh giá gửi không thành công', Notification::ERROR);
            Log::error($e->getMessage());
            return back()
                ->with('exception', $e->getMessage())
                ->with($this->notification->getMessage())
                ->withInput();
        }
    }

    public function thank()
    {
        return view('admin.bookings.thank');
    }

    /**
     * Store booking
     *
     * @param Request $request
     * @param $slug
     * @param Tour $tourModel
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeBooking(Request $request, $slug, Tour $tourModel)
    {
        $tour = $tourModel->getTourBySlug($slug);
        data_set($request, 'date', $request->departure_time);
        $checkRoom = json_decode($this->checkRoom($request, $slug)->content(), true);
        $roomRule = [];

        if (!empty($request->room)) {
            foreach ($request->room as $index => $room) {
                $targetMax = $checkRoom['room_available'][$room['id']];
                $roomRule["room.{$index}.number"] = 'nullable|integer|min:0|max:' . $targetMax;
            }
        }

        $validateRule = array_merge($this->clientService->ruleBooking(), $roomRule);

        $request->validate($validateRule, [
            'room.*.number.max' => 'Số phòng đã vượt quá giới hạn cho phép',
            'room.*.number.min' => 'Vui lòng chọn số phòng phù hợp',
            'followers.*.name.required_with' => 'Vui lòng nhập tên người đi theo',
            'followers.*.age.required_with' => 'Vui lòng nhập tuổi người đi theo',
            'followers.*.relationship.required_with' => 'Vui lòng nhập liên hệ người đi theo',
        ], [
            'first_name' => 'tên',
            'last_name' => 'họ',
            'phone' => 'điện thoại',
            'number_adults' => 'số người lớn',
            'number_children' => 'số trẻ em',
            'departure_time' => 'ngày',
            'payment_method' => 'loại thanh toán',
            'address' => 'địa chỉ',
            'city' => 'thành phố',
            'province' => 'huyện',
            'country' => 'quốc gia',
            'identification' => 'Căn cước công dân',
        ]);

        $this->notification->setMessage('Đặt tour thành công', Notification::SUCCESS);

        DB::beginTransaction();
        try {
            $booking = $this->clientService->storeBooking($request, $tour);
            $dataNotification = [
                'content' => 'KH ' . $booking->customer->name . ' vừa booking tour ' . $booking->tour->name,
                'url' => route('bookings.show', $booking->id),
            ];
            NotifyService::sendNotifyToAdmin($dataNotification);

            if ($request->payment_method == PAYMENT_CASH) {
                DB::commit();
                dispatch(new SendMailBookingJob($booking));
                return response()->json($this->notification->getMessage());
            }

            if ($request->payment_method == PAYMENT_VNPAY) {
                $orderIDVnPay = 'VNPay' . time();
                $booking->invoice_no = $orderIDVnPay;
                $booking->save();

                $response = VNPayPayment::purchase([
                    'redirectUrl' => route('booking.vnpay.redirect'),
                    'orderId' => $orderIDVnPay,
                    'amount' => strval($booking->total),
                    'orderInfo' => 'Thanh toan tour GoodTrip',
                ]);

                DB::commit();
                return response()->json([
                    'url' => $response,
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->notification->setMessage('Đặt tour không thành công', Notification::ERROR);
        }

        return response()->json($this->notification->getMessage());
    }

    public function redirectVnPay(Request $request)
    {
        $checkPayment = VNPayPayment::completePurchase($request);
        $orderId = $request->vnp_TxnRef;
        $transId = $request->vnp_TransactionNo;
        $notification = array(
            'message' => $checkPayment['message'],
            'alert-type' => 'error',
        );
        $booking = Booking::where('invoice_no', $orderId)->first();
        if ($booking != null) {
            if ($checkPayment['success']) {
                $booking->is_payment = PAYMENT_PAID;
                $booking->transaction_id = $transId;
                $booking->deposit = $booking->total;
                $booking->save();
                $notification = array(
                    'message' => 'Đặt hàng thành công',
                    'alert-type' => 'success',
                );
                dispatch(new SendMailBookingJob($booking));
            } else {
                $tour = $booking->tour;
                $numberAdults = $booking->number_adults;
                $numberChildren = $booking->number_children;
                $departureTime = $booking->departure_time;
                $roomId = $booking->room_id;
                $numberRoom = $booking->number_room;
                $errorPayment = $notification['message'];

                return view('booking', compact([
                    'tour',
                    'numberAdults',
                    'numberChildren',
                    'departureTime',
                    'roomId',
                    'numberRoom',
                    'booking',
                    'errorPayment'
                ]));
            }

        } else {
            $notification['message'] = 'Mã hóa đơn không đúng';
        }
        return redirect()->route('booking.thank')->with($notification);
    }

    public function checkRoom(Request $request, $slug)
    {
        $request->validate([
            'departure_time' => 'required|date_format:Y-m-d',
        ]);
        $tourModel = new Tour();
        $tour = $tourModel->getTourBySlug($slug);
        $offsetDate = ($tour->duration - 1) * -1;
        $startDate = Carbon::parse($request->departure_time)->addDays($offsetDate);
        $endDate = Carbon::parse($request->departure_time);
        $bookings = Booking::with('booking_room')
            ->where('status', '!=', BOOKING_CANCEL)
            ->whereDate('departure_time', '>=', $startDate)
            ->whereDate('departure_time', '<=', $endDate)
            ->where('tour_id', $tour->id)
            ->get();

        $roomAvailable = [];
        foreach ($tour->rooms as $room) {
            $roomAvailable[$room->id] = $room->number;
        }

        foreach ($bookings as $booking) {
            foreach ($booking->booking_room as $bookingRoom) {
                $roomAvailable[$bookingRoom->room_id] -= $bookingRoom->number;
                if ($roomAvailable[$bookingRoom->room_id] < 0) {
                    $roomAvailable[$bookingRoom->room_id] = 0;
                }
            }
        }

        return response()->json([
            'date' => $request->departure_time,
            'room_available' => $roomAvailable,
        ]);
    }

    public function order(Request $request)
    {
        try {
            $token = $request->token;
            $bookingId = decrypt($token);
            $booking = Booking::where('id', $bookingId)->first();
            $linkTour = route('client.tours.detail', $booking->tour->slug);
            $linkComment = route('client.tours.detail', $booking->tour->slug) . '?token=' . encrypt($bookingId);
            $linkQrCode = $booking->status == BOOKING_COMPLETE ? $linkComment : $linkTour;
            $qrCode = QrCode::format('png')->size(300)->generate($linkQrCode);
            return view('order', compact(['booking', 'qrCode', 'linkQrCode']));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            abort(404);
        }
    }

    public function cancelBooking(Request $request, $id)
    {
        $booking = app(Booking::class);
        $request->validate($booking->rule());
        return json_encode($booking->updateStatus($request, $id));
    }
}
