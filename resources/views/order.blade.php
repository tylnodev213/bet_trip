@extends('layouts.client')
@section('content')
    <link href="{{ asset('css/bootstrap-v5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/toastr.css') }}">
    <div class="banner-title mb-5">
        <img src="{{ asset('images/page-title.jpg') }}" alt="banner title">
        <p class="title">Thông tin booking</p>
    </div>
    <div class="container mb-4">
        <br /><br />
        <ul class="list-unstyled multi-steps">
            <li>Booking</li>
            <li class="{{ $booking->status == BOOKING_CONFIRM ? 'is-active' : '' }}">Xác nhận</li>
            <li class="{{ $booking->status == BOOKING_COMPLETE || $booking->status == BOOKING_CANCEL ? 'is-active' : '' }} {{ $booking->status == BOOKING_CANCEL ? 'is-cancel' : '' }}">{{ $booking->status == BOOKING_CANCEL ? 'Đã hủy' : 'Hoàn thành' }}</li>
        </ul>
    </div>

    <!-------------------- Contact -------------------->
    <div class="container mb-4">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title">Thông tin người đặt</h4>
                        <hr>
                        <table>
                            <tr>
                                <td class="tb-title" width="150px">Tên:</td>
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
                                <td class="tb-title">Số CCCD:</td>
                                <td>{{  $booking->customer->identification  }}</td>
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
                                <td class="tb-title" width="150px";>Tour:</td>
                                <td>{{ $booking->tour->name }}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Thời gian:</td>
                                <td>{{ date('d/m/Y',strtotime($booking->departure_time)) }} ~ {{ \Carbon\Carbon::parse($booking->departure_time)->addDays($booking->tour->duration)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="tb-title">Thanh toán:</td>
                                <td>
                                    @switch($booking->payment_method)
                                        @case(1)
                                            Tiền mặt
                                            @break
                                        @case(2)
                                            VnPay
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td class="tb-title">Giá:</td>
                                <td>
                                    Người lớn: {{ number_format($booking->tour->price_adult) . ' đ'}}<br/>
                                    Trẻ em: {{ number_format($booking->tour->price_child) . ' đ'}}
                                </td>
                            </tr>
                            <tr>
                                <td class="tb-title">Số người:</td>
                                <td>
                                    {{ $booking->number_adults }} người lớn {{ !empty($booking->number_children) ? 'và ' . $booking->number_children . ' trẻ em' : '' }}
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
                            @if($booking->status == BOOKING_CANCEL)
                            <tr>
                                <td class="tb-title">Số tiền hoàn:</td>
                                <td>{{ number_format($booking->refund) . ' đ'}}</td>
                            </tr>
                            @endif
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
                                    <th scope="col">Tổng tiền</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($booking->rooms as $room)
                                    @php $index = 1 @endphp
                                    @if ($room->pivot->number > 0)
                                        <tr>
                                            <td>{{ $index  }}</td>
                                            <td>{{ $room->name }}</td>
                                            <td>
                                                {{ $room->pivot->number }}
                                            </td>
                                            <td>{{ number_format($room->pivot->price) }}đ</td>
                                            <td>{{ number_format($room->pivot->number * $room->pivot->price) }}đ</td>
                                        </tr>
                                        @php $index++ @endphp
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="box-info">
            <!-- tab -->
            <!-- panel -->
            <div class="tab-content" id="pills-tabContent">
                <!-- panel descriptions -->
                <div class="tab-pane panel-desc fade active show" id="pills-desc" role="tabpanel"
                     aria-labelledby="pills-desc-tab">
                    @if (!empty($booking->tour->itineraries) && $booking->tour->itineraries->isNotEmpty())
                        <div class="box-text">
                            <p class="panel-title">
                                {{ __('client.itinerary') }}
                            </p>
                            <!-- Accordion Itinerary -->
                            <div class="accordion" id="accordionItinerary">
                                @foreach($booking->tour->itineraries as $itinerary)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header"
                                            id="panelsItineraryHeading{{$loop->index}}">
                                            <button
                                                class="accordion-button {{ $loop->first ?: 'collapsed'  }}"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#panelsItineraryCollapse{{$loop->index}}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="panelsItineraryCollapse{{$loop->index}}">
                                                {{ __('client.detail.day') . ' ' . $loop->index + 1}}
                                                : {{ $itinerary->name }}
                                                ({{ $itinerary->places->count() . ' ' . __('client.detail.stops')}}
                                                )
                                            </button>
                                        </h2>
                                        <div id="panelsItineraryCollapse{{$loop->index}}"
                                             class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                             aria-labelledby="panelsItineraryHeading{{$loop->index}}">
                                            <div class="accordion-body">
                                                <ul class="list-accordion">
                                                    @foreach($itinerary->places as $place)
                                                        <li class="list-accordion-item">
                                                            <p class="title-item"> {{ $place->name }}</p>
                                                            {!! $place->description !!}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (!empty($booking->tour->map))
                        <div class="box-text">
                            <p class="panel-title">
                                Maps
                            </p>
                            <div class="box-maps">
                                {!! $booking->tour->map !!}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <div class="container">
            <div>
                @if (in_array($booking->status, [BOOKING_NEW, BOOKING_CONFIRM]))
                <ul class="list-policy">
                    <li>Vui lòng liên hệ với chúng tôi trong giờ hành chính vào các ngày trong tuần (trừ Chủ nhật) nếu bạn muốn thay đổi thông tin, số vé, số phòng, hay hủy tour</li>
                    <li> Bạn có thể hủy miễn phí đơn hàng trước 3 ngày trước khi chuyến hành trình
                        được bắt đầu. Nếu còn ít hơn 3 ngày bạn sẽ bị mất 20% tiền cọc.
                    </li>
                    <li>
                        Khi bạn thành toán với VNPay, bạn xác nhận rằng bạn đã đọc về các ràng buộc
                        thanh toán của VNPay.
                    </li>
                    <li> Điều khoản Sử dụng, Chính sách Bảo mật của Khách hàng, cùng với các quy tắc
                        của nhà điều hành tour & quy định <a href="javascript:void(0);" data-toggle="modal" data-target="#ruleModal">(xem chính sách và điều khoản để biết thêm chi tiết)</a> .
                    </li>
                </ul>
                @elseif($booking->status == BOOKING_CANCEL)
                    <ul class="list-policy">
                        <li>Tiền sẽ được hoàn trong 1 đến 5 ngày làm việc tới, nhân viên của chúng tôi sẽ liên hệ cho bạn. Nếu bạn thanh toán qua VnPay, có thể sẽ phải mất 1 đến 7 ngày làm việc của ngân hàng.</li>
                        <li>Thật tiếc khi không được phục vụ cho bạn lần này. Hy vọng trong tương lai bạn vẫn sẽ tin tưởng vào dịch vụ của chúng tôi. Nếu có vấn đề gì cho chúng tôi biết ý kiến của bạn <a href="{{ route('client.contact.index') }}">tại đây</a></li>
                    </ul>
                @else
                    <ul class="list-policy">
                        <li>Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của chúng tôi!</li>
                    </ul>
                @endif
            </div>
            @if (in_array($booking->status, [BOOKING_NEW, BOOKING_CONFIRM])  && $booking->departure_time > date('Y-m-d'))
                <div class="d-flex justify-content-end">
                    <button class="btn btn-danger" onclick="cancelBooking()">Hủy Booking</button>
                </div>
            @endif
        </div>
    </div>
    <!-------------------- End Contact -------------------->

    <!-------------------- List Tours -------------------->

    <!-------------------- End List Tours-------------------->
@endsection
@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if($errors->any())
        document.getElementById("formContact").scrollIntoView();
        @endif
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success m-2',
                cancelButton: 'btn btn-danger m-2'
            },
            buttonsStyling: false
        });

        function cancelBooking() {
            swalWithBootstrapButtons.fire({
                title: 'Bạn có chắc chắn?',
                text: `Bạn muốn hủy lịch đặt tour này!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Vâng, xác nhận',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('order.cancel', $booking->id) }}',
                        method: 'POST',
                        dataType: 'json',
                        data: {'status': {{ BOOKING_CANCEL }} },
                        success: function (response) {
                            if (response) {
                                location.reload(true);
                            } else {
                                toastrMessage('error', 'Hủy không thành công, Vui lòng liên hệ lại với chúng tôi');
                            }
                        }
                    });
                }
            })
        }
    </script>
@endsection
