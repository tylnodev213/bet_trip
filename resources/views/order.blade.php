@extends('layouts.client')
@section('content')
    <div class="banner-title mb-5">
        <img src="{{ asset('images/page-title.jpg') }}" alt="banner title">
        <p class="title">Thông tin booking</p>
    </div>
    <div class="container-fluid">
        <br /><br />
        <ul class="list-unstyled multi-steps">
            <li>Booking</li>
            <li class="{{ $booking->status == BOOKING_CONFIRM ? 'is-active' : '' }}">Xác nhận</li>
            <li class="{{ $booking->status == BOOKING_COMPLETE ? 'is-active' : '' }}">Hoàn thành</li>
        </ul>
    </div>

    <!-------------------- Contact -------------------->
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title">Ngày tạo: {{  $booking->created_at->format('d/m/Y') }}</h4>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title">Thông tin người đặt</h4>
                        <hr>
                        <table>
                            <tr>
                                <td class="tb-title" width="30%">Tên:</td>
                                <td>{{ $booking->customer->first_name . ' ' . $booking->customer->last_name }}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Email:</td>
                                <td>{{ $booking->customer->email }}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Điện thoại:</td>
                                <td>{{ $booking->customer->phone }}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Địa chỉ:</td>
                                <td>
                                    {{ trim(implode(", ", [
                                        $booking->customer->address,
                                         $booking->customer->province,
                                         $booking->customer->city,
                                         $booking->customer->country
                                         ]),', ')  }}
                                </td>
                            </tr>
                            <tr>
                                <td class="tb-title">Zipcode:</td>
                                <td>{{  $booking->customer->zipcode  }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if($booking->requirement)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Yêu cầu</h4>
                            <hr>
                            <p> {{ $booking->requirement }} </p>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-9 mb-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title d-flex justify-content-between align-items-center">
                            <span>Thông tin đặt tour</span>
                        </h4>
                        <hr>
                        <table>
                            <tr>
                                <td class="tb-title" width="30%";>Tour:</td>
                                <td>{{ $booking->tour->name }}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Thời gian:</td>
                                <td>{{ date('d/m/Y',strtotime($booking->departure_time)) }}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Thanh toán:</td>
                                <td>
                                    @switch($booking->payment_method)
                                        @case(1)
                                            Tiền mặt
                                            @break
                                        @case(2)
                                            Momo
                                            @break
                                        @case(3)
                                            VnPay
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td class="tb-title">Giá:</td>
                                <td>{{ number_format($booking->price) . ' đ'}}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Số người:</td>
                                <td>
                                    {{ $booking->people }}
                                </td>
                            </tr>
                            <tr>
                                <td class="tb-title">Giảm giá:</td>
                                <td>{{ $booking->discount }}%</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Tổng:</td>
                                <td>{{ number_format($booking->total) . ' đ'}}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Đã thanh toán:</td>
                                <td>{{ number_format($booking->deposit) . ' đ'}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-3 mb-4">
                <div class="text-center" style="width: 300px">{{ $booking->status == BOOKING_COMPLETE ? 'Cho chúng mình xin biết về trải nghiệm của bạn nhé !' : 'Quét để hiển thị tour đã đặt' }} <a href="{{ $linkQrCode }}">tại đây</a></div>
                <img src="data:image/png;base64,{{ base64_encode($qrCode) }}">
            </div>
        </div>

        @if($booking->booking_room->where('number', '>', 0)->isNotEmpty())
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Thông tin phòng</h4>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tên phòng</th>
                                    <th scope="col">Số lượng</th>
                                    <th scope="col">Giá phòng</th>
                                    <th scope="col">Tông tiền</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($booking->rooms as $room)
                                    @if ($room->pivot->number > 0)
                                        <tr>
                                            <td>{{ $loop->index + 1  }}</td>
                                            <td>{{ $room->name }}</td>
                                            <td>
                                                {{ $room->pivot->number }}
                                            </td>
                                            <td>{{ number_format($room->pivot->price) }}đ</td>
                                            <td>{{ number_format($room->pivot->number * $room->pivot->price) }}đ</td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!-------------------- End Contact -------------------->

    <!-------------------- List Tours -------------------->

    <!-------------------- End List Tours-------------------->
@endsection
@section('js')
    <script>
        @if($errors->any())
        document.getElementById("formContact").scrollIntoView();
        @endif
    </script>
@endsection
