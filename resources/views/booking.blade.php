@extends('layouts.client')
@section('content')
    <link href="{{ asset('css/bootstrap-v5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/toastr.css') }}">

    <!-------------------- Checkout -------------------->
    <div class="box-checkout box-detail-tour" style="margin-top: 100px">
        <div class="container">
            <form id="formCheckout" action="{{ route('client.booking.store', $tour->slug) }}" method="post">
                <input type="hidden" value="{{ $booking ? $booking->id : '' }}" id="bookingId" name="booking_id">
                <input type="hidden" value="{{ $tour->price_adult }}" id="price_adult">
                <input type="hidden" value="{{ $tour->price_child }}" id="price_child">
                <input type="hidden" value="{{ $tour->duration }}" id="duration">
                @csrf
                <p class="title-checkout">Thông tin đặt tour</p>
                <div class="row box-detail-content box-checkout-content">
                    <div class="col-12 col-lg-7 col-xl-8">
                        <div class="box-body-checkout">

                            <hr>
                            <!-- checkout detail -->
                            <div class="box-checkout-item infoCustomer">
                                <p class="header-checkout">Thông tin khách hàng</p>
                                <p class="header-desc">Chúng tôi cần một số thông tin để xác nhận chuyến tham của
                                    bạn</p>
                                <div class="sub-checkout-item">
                                    <p class="sub-header">Người đặt </p>
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="firstName" class="form-label title">Tên <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="firstName"
                                                   placeholder="Tên" name="first_name"
                                                   value="{{ old('first_name', $booking ? $booking->customer->first_name : data_get($customer, 'first_name')) }}">
                                            <p class="text-danger" id="errorFirstName"></p>
                                            @error('first_name')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="lastName" class="form-label title">Họ<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="lastName"
                                                   placeholder="Họ" name="last_name"
                                                   value="{{ old('last_name', $booking ? $booking->customer->last_name : data_get($customer, 'last_name')) }}">
                                            <p class="text-danger" id="errorLastName"></p>
                                            @error('last_name')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="email" class="form-label title">Email</label>
                                            <input type="text" class="form-control" id="email"
                                                   placeholder="email@domain.com" name="email"
                                                   value="{{ old('email', $booking ? $booking->customer->email : data_get($customer, 'email')) }}">
                                            <p class="text-danger" id="errorEmail"></p>
                                            @error('email')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="phone" class="form-label title">Số điện thoại<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="phone"
                                                   placeholder="Số điện thoại"
                                                   name="phone"
                                                   value="{{ old('phone', $booking ? $booking->customer->phone : data_get($customer, 'phone')) }}">
                                            <p class="text-danger" id="errorPhone"></p>
                                            @error('phone')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="sub-checkout-item">
                                    <p class="sub-header">Người đi cùng (không bắt buộc)</p>
                                    <div class="row touristBlock" data-index="0">
                                        <div class="col-6">
                                            <label for="name" class="form-label title">Họ Tên </label>
                                            <input type="text" class="form-control" id="name"
                                                   placeholder="Họ tên" name="followers[0][name]"
                                                   value="{{ old('name', '') }}">
                                            <p class="text-danger followers-name"></p>
                                        </div>
                                        <div class="col-6">
                                            <label for="age" class="form-label title">Tuổi </label>
                                            <input type="text" class="form-control" id="age"
                                                   placeholder="" name="followers[0][age]"
                                                   value="{{ old('age', '') }}">
                                            <p class="text-danger followers-age"></p>
                                        </div>
                                        <div class="col-6">
                                            <label for="identification-follower" class="form-label title">Số CCCD (không bắt buộc)</label>
                                            <input type="text" class="form-control" id="identification-follower"
                                                   placeholder="Số Căn cước công dân"
                                                   name="followers[0][identification]"
                                                   value="{{ old('identification', '') }}">
                                            <p class="text-danger followers-identification"></p>
                                        </div>
                                        <div class="col-6">
                                            <label for="relationship" class="form-label title">Liên hệ</label>
                                            <input type="text" class="form-control" id="relationship"
                                                   placeholder="email hoặc số điện thoại"
                                                   name="followers[0][relationship]"
                                                   value="{{ old('relationship', '') }}">
                                            <p class="text-danger followers-relationship"></p>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary addTourist">Thêm người đi</button>
                                </div>

                                <div class="sub-checkout-item">
                                    <p class="sub-header">Địa chỉ </p>
                                    <div class="row">
                                        <div class="col-12">
                                            <label for="address" class="form-label title">Địa chỉ của bạn</label>
                                            <input type="text" class="form-control" id="address"
                                                   placeholder="Địa chỉ của bạn" name="address"
                                                   value="{{ old('address', $booking ? $booking->customer->address : data_get($customer, 'address')) }}">
                                            <p class="text-danger" id="errorAddress"></p>
                                            @error('address')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="city" class="form-label title">Thành phố </label>
                                            <input type="text" class="form-control" id="city"
                                                   placeholder="Thành phố của bạn"
                                                   name="city"
                                                   value="{{ old('city', $booking ? $booking->customer->city : data_get($customer, 'city')) }}">
                                            <p class="text-danger" id="errorCity"></p>
                                            @error('city')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="province"
                                                   class="form-label title">Huyện / Quận </label>
                                            <input type="text" class="form-control" id="province"
                                                   placeholder="Huyện" name="province"
                                                   value="{{ old('province', $booking ? $booking->customer->province : data_get($customer, 'province')) }}">
                                            <p class="text-danger" id="errorProvince"></p>
                                            @error('province')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="identification" class="form-label title">Số CCCD</label>
                                            <input type="text" class="form-control" id="identification"
                                                   placeholder="Mã CCCD" name="identification"
                                                   value="{{ old('identification', $booking ? $booking->customer->identification : data_get($customer, 'identification')) }}">
                                            <p class="text-danger" id="errorIdentification"></p>
                                            @error('identification')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="country" class="form-label title">Quốc gia</label>
                                            <input type="text" class="form-control" id="country"
                                                   placeholder="Quốc gia" name="country"
                                                   value="{{ old('country', 'Việt Nam') }}">
                                            <p class="text-danger" id="errorContry"></p>
                                            @error('country')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="sub-checkout-item">
                                        <p class="sub-header">
                                            <label for="requirement" class="form-label">Yêu cầu của khách hàng</label>
                                        <p class="text-danger" id="errorRequirement"></p>
                                        <div class="row">
                                            <div class="col-12">
                                                <textarea type="text" class="form-control" id="requirement"
                                                          placeholder="Yêu cầu" rows="5"
                                                          name="requirement">{{ old('requirement', $booking ? $booking->requirement : '') }}</textarea>
                                            </div>
                                        </div>
                                        @error('requirement')
                                        <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <!-- Payment method -->
                            <div class="box-checkout-item">
                                <hr>
                                <p class="header-checkout">Phương thức thanh toán</p>

                                <div class="sub-checkout-item paymentMethod">
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal-vnpay"
                                               value="2" checked>
                                        <label class="form-check-label" for="paypal-vnpay">
                                            <span class="payment-title">Ví VN PAY</span>
                                            <img class="payment-image"
                                                 src="{{ asset('images/icon/logo-vnpay.png') }}"
                                                 alt="vnpay">
                                        </label>
                                    </div>
                                    @if(env('ENABLE_CASH'))
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cash"
                                                   value="1" checked>
                                            <label class="form-check-label" for="cash">
                                                <span class="payment-title">Bằng tiền mặt</span>
                                                <img class="payment-image"
                                                     src="{{ asset('images/icon/cash.png') }}"
                                                     alt="cash">
                                            </label>
                                        </div>
                                    @endif
                                </div>
                                @error('payment_method')
                                <p class="text-danger">{{ $message }}</p>
                                @enderror
                                <div class="sub-checkout-item">
                                    <ul class="list-policy">
                                        <li> Bạn sẽ bị tính tổng số tiền sau khi đơn đặt hàng của bạn được xác nhận.
                                        </li>
                                        <li> Nếu xác nhận không nhận được ngay lập tức, chúng tôi sẽ liên hệ vợi bạn sớm
                                            nhất có thể. Chậm nhất là 1 ngày làm việc kể cả thứ 7, chủ nhật và
                                            ngày lễ
                                        </li>
                                        <li> Bạn có thể hủy miễn phí đơn hàng trước 3 ngày trước khi chuyến hành trình
                                            được bắt đầu. Nếu còn ít hơn 3 ngày bạn sẽ bị mất 20% tiền cọc.
                                        </li>
                                        <li>
                                            Khi bạn thành toán với VNPay, bạn xác nhận rằng bạn đã đọc về các ràng buộc
                                            thanh toán của VNPay
                                        </li>
                                        <li> Điều khoản Sử dụng, Chính sách Bảo mật của Khách hàng, cùng với các quy tắc
                                            của nhà điều hành tour & quy định<a href="javascript:void(0);" data-toggle="modal" data-target="#ruleModal">(xem danh sách để biết thêm chi tiết)</a> .
                                        </li>
                                    </ul>
                                </div>

                                <div class="sub-checkout-item bookingBtn">
                                    <button type="submit" class="btn-submit-checkout" id="btnSubmitCheckout">
                                        Đặt tour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-------------------- Form Coupon -------------------->
                    <div class="col-12 col-lg-5 col-xl-4 infoTour">
                        <div class="box-book-now box-coupon">
                            <div class="wrap-content-coupon">
                                <span class="card-title">{{ $tour->name }} </span>
                                <p class="card-text">Trẻ em (dưới 16 tuổi): <span
                                        class="card-title">{{ number_format($tour->price_child) }} VNĐ</span>

                                <p class="card-text">Người lớn: <span
                                        class="card-title">{{ number_format($tour->price_adult) }} VNĐ</span>
                                <p class="text-content mt-2">
                                    <img src="{{ asset('images/icon/location.svg') }}" alt="location">
                                    <span>{{ $tour->destination->name   }}</span>
                                </p>
                                <div class="info-tour d-flex justify-content-between">
                                    <span class="card-text w-50">
                                    Thời gian:
                                        <p
                                            class="card-title">{{ \App\Libraries\Utilities::durationToString($tour->duration) }}
                                        </p>
                                    </span>
                                    <span class="card-text w-50">
                                    Thể loại: <p class="card-title">{{ $tour->type->name }}</p>
                                </span>
                                </div>

                                <div class="input-inner-icon">
                                    <img src="{{ asset('images/icon/schedule.svg') }}" alt="departure">
                                    <input type="hidden"
                                           value="{{ old('departure_time', $departureTime) }}"
                                           name="departure_time"
                                           id="inputDepartureTime">
                                    <div id="departureTimePicker">
                                        <input class="form-control" type="text" id="departureTime">
                                        @error('departure_time')
                                        <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <p class="text-danger" id="errorDepartureTime"></p>
                                <div class="input-inner-icon">
                                    <img src="{{ asset('images/icon/people.svg') }}" alt="people">
                                    <select class="form-control" id="selectNumberAdults" name="number_adults">
                                        @for($i = 1; $i <= 20; $i++)
                                            <option
                                                value="{{ $i }}" {{ (old('number_adults', $numberAdults) == $i) ? 'selected' : '' }}>{{ $i }}
                                                Người lớn
                                            </option>
                                        @endfor
                                    </select>
                                    @error('number_adults')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="input-inner-icon">
                                    <img src="{{ asset('images/icon/people.svg') }}" alt="people">
                                    <select class="form-control" id="selectNumberChildren" name="number_children">
                                        @for($i = 0; $i <= 20; $i++)
                                            <option
                                                value="{{ $i }}" {{ (old('number_children', $numberChildren) == $i) ? 'selected' : '' }}>{{ $i }}
                                                Trẻ em
                                            </option>
                                        @endfor
                                    </select>
                                    @error('number_children')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="input-inner-icon couponBlock">
                                    <div class="row">
                                        <input type="hidden" id="linkCheckCoupon" value="{{ route('coupons.check') }}">
                                        <input type="hidden" name="codeCoupon" id="codeCoupon"
                                               value="">
                                        <div class="col-7">
                                            <input class="form-control" style="padding-left: 30px" type="text"
                                                   placeholder="Code" id="coupon">
                                            <input type="hidden" id="discountCoupon" value="0">
                                        </div>
                                        <div class="col-5">
                                            <button type="button" id="btnCouponSubmit" class="btn-apply-coupon">Xác nhận</button>
                                        </div>
                                    </div>
                                </div>

                                <!--- PHÒNG -->
                                <h5>Loại phòng</h5>
                                <div class="input-inner-icon">
                                    @foreach($tour->rooms as $room)
                                        @php
                                            $numberRoom = 0;
                                        @endphp
                                        @foreach($listRooms ?? [] as $roomItem)
                                            @if($roomItem['id'] == $room->id)
                                                @php
                                                    $numberRoom = $roomItem['number'];
                                                @endphp
                                            @endif
                                        @endforeach
                                        <h6>{{ $room->name . ' - ' . number_format($room->price) . 'đ' }}</h6>
                                        <h7 style="color: grey">Còn
                                            <span id="roomAvailable{{ $room->id }}"></span> phòng
                                        </h7>
                                        <input type="hidden" min="0" class="selectRoom"
                                               name="room[{{ $loop->index }}][id]" value="{{ $room->id }}">
                                        <div class="input-inner-icon">
                                            <img src="{{ asset('images/icon/number.svg') }}" alt="people">
                                            <input type="number" class="form-control numberRoom"
                                                   name="room[{{ $loop->index }}][number]"
                                                   data-price="{{ $room->price }}"
                                                   value="{{ $numberRoom }}"
                                                   id="numberRoom{{ $room->id }}"
                                                   placeholder="Số lượng phòng">
                                        </div>
                                        <p class="text-danger" id="room-{{ $loop->index }}-number"></p>
                                        @error('room.' . $loop->index . '.number')
                                        <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    @endforeach
                                </div>

                            </div>
                            <div class="total-price-coupon d-flex justify-content-between align-items-center">
                                <span class="card-text">
                                    Tổng
                                </span>
                                <span class="card-title">
                                        <p class="d-none" id="priceAfterDiscount"
                                           style="text-decoration: line-through; color: grey"></p>
                                    <span id="totalPrice"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-------------------- End Form Coupon -------------------->
                </div>

            </form>
        </div>
    </div>
    <!-------------------- End Checkout -------------------->

    <!-------------------- Thanks -------------------->
    <div class="modal fade thank-modal" id="thanksModal" data-bs-backdrop="static" data-bs-keyboard="false"
         tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="panel-thank modal-content d-flex justify-content-center align-items-center flex-column">
                <p class="thank-title">Cảm ơn!</p>
                <p class="thank-text">Bạn đã đặt tour thành công.</p>
                <p class="thank-text">Thông tin chi tiết về giá cả và đặt xe,</p>
                <p class="thank-text"> sẽ được nhân viên liên hệ với bạn vào thời gian sớm nhất.</p>
                <button class="btn-back-home"><a class="d-flex align-items-center justify-content-center"
                                                 href="{{ route('index') }}">Quay lại trang chủ</a></button>
            </div>
        </div>
    </div>


    <!-------------------- End Thanks -------------------->

    <!-------------------- Other Data -------------------->
    <input type="hidden" value="{{ route('client.booking.check-room', $tour->slug) }}" id="linkCheckRoom">
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <script>
        const driver = window.driver.js.driver;
        const driverObj = driver({
            showProgress: true,
            steps: [
                { element: '.infoCustomer', popover: { title: 'Bước 1: ', description: 'Điền thông tin cá nhân của bạn tại đây' } },
                { element: '.infoTour', popover: { title: 'Bước 2: ', description: 'Lựa chọn thông tin chuyến đi theo nhu cầu của bạn' } },
                { element: '.couponBlock', popover: { title: 'Mã giảm giá: ', description: 'Nhập mã giảm giá của bạn và áp dụng bằng cách nhấn nút Xác nhận' } },
                { element: '.paymentMethod', popover: { title: 'Bước 3: ', description: 'Chọn phương thức thanh toán linh hoạt' } },
                { element: '.bookingBtn', popover: { title: 'Hoàn thành', description: 'Click vào Đặt tour để tiến hành thanh toán và hoàn thành đặt tour' } },
            ],
        });

        driverObj.drive();
    </script>
    <script>
        @isset($errorPayment)
        toastr.error('{{ $errorPayment }}');
        @endisset
    </script>
    <script>
        for (const [key, value] of Object.entries(JSON.parse('{!! $roomAvailable !!}').room_available)) {
            $('#roomAvailable' + key).text(value);
            $('#numberRoom' + key).prop('max', value);
        }
        disableSubmitButton('#formCheckout');

        $('#formCheckout').on('submit', function (e) {
            e.preventDefault();
            let link = $(this).attr('action');
            let formData = new FormData(document.getElementById('formCheckout'));
            $('#errorFirstName').text('');
            $('#errorLastName').text('');
            $('#errorEmail').text('');
            $('#errorPhone').text('');
            $('#errorAddress').text('');
            $('#errorCity').text('');
            $('#errorProvince').text('');
            $('#errorIdentification').text('');
            $('#errorContry').text('');
            $('#errorRequirement').text('');
            $('#errorDepartureTime').text('');
            $('.followers-age, .followers-name, .followers-identification, .followers-relationship').text('');

            $.ajax({
                url: link,
                type: 'post',
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    enableSubmitButton('#formCheckout', 300);
                    let type = response['alert-type'];
                    let message = response['message'];
                    let url = response['url'];

                    if (type === 'success') {
                        $('#thanksModal').modal('show');
                        return;
                    }

                    if (url) {
                        window.location.href = url;
                        return;
                    }

                    toastrMessage(type, message);
                },
                error: function (jqXHR) {
                    enableSubmitButton('#formCheckout', 300);
                    let response = jqXHR.responseJSON;

                    if (response?.errors?.first_name !== undefined) {
                        $('#errorFirstName').text(response.errors.first_name[0]);
                    }

                    if (response?.errors?.last_name !== undefined) {
                        $('#errorLastName').text(response.errors.last_name[0]);
                    }

                    if (response?.errors?.email !== undefined) {
                        $('#errorEmail').text(response.errors.email[0]);
                    }

                    if (response?.errors?.phone !== undefined) {
                        $('#errorPhone').text(response.errors.phone[0]);
                    }

                    if (response?.errors?.address !== undefined) {
                        $('#errorAddress').text(response.errors.address[0]);
                    }

                    if (response?.errors?.city !== undefined) {
                        $('#errorCity').text(response.errors.city[0]);
                    }

                    if (response?.errors?.province !== undefined) {
                        $('#errorProvince').text(response.errors.province[0]);
                    }

                    if (response?.errors?.identification !== undefined) {
                        $('#errorIdentification').text(response.errors.identification[0]);
                    }

                    if (response?.errors?.country !== undefined) {
                        $('#errorContry').text(response.errors.country[0]);
                    }

                    if (response?.errors?.requirement !== undefined) {
                        $('#errorRequirement').text(response.errors.requirement[0]);
                    }

                    if (response?.errors?.departure_time !== undefined) {
                        $('#errorDepartureTime').text(response.errors.departure_time[0]);
                    }

                    $('.touristBlock').each(function (index, item) {
                        if (response?.errors['followers.' + index + '.name'] !== undefined) {
                            $(item).find(`.followers-name`).text(response.errors['followers.' + index + '.name'][0]);
                        }
                        if (response?.errors['followers.' + index + '.age'] !== undefined) {
                            $(item).find(`.followers-age`).text(response.errors['followers.' + index + '.age'][0]);
                        }
                        if (response?.errors['followers.' + index + '.identification'] !== undefined) {
                            $(item).find(`.followers-identification`).text(response.errors['followers.' + index + '.identification'][0]);
                        }
                        if (response?.errors['followers.' + index + '.relationship'] !== undefined) {
                            $(item).find(`.followers-relationship`).text(response.errors['followers.' + index + '.relationship'][0]);
                        }
                    });

                    $('.numberRoom').each(function (index, item) {
                        if (response?.errors['room.' + index + '.number'] !== undefined) {
                            $(`#room-${index}-number`).text(response.errors['room.' + index + '.number'][0]);
                        }
                    });


                    document.getElementById("formCheckout").scrollIntoView();
                }
            });
        });

        $(document).on('click', '.addTourist', function (e) {
            e.preventDefault();
            let lastItem = $(this).closest('.sub-checkout-item').find('.touristBlock:last');
            let numberItem = $(this).closest('.sub-checkout-item').find('.touristBlock').length;
            let clone = lastItem.clone(true);
            clone.attr('data-index', numberItem);
            clone.find('input').each(function () {
                let currentName = $(this).attr('name');
                let newName = currentName.replace(numberItem - 1, numberItem);
                $(this).attr('name', newName);
                $(this).val('');
            });
            clone.find('p.text-danger').remove();
            if (!clone.find('.removeTourist').length) {
                clone.append('<div class="mb-2 d-flex justify-content-end"><div class="col-1"><button class="btn btn-danger removeTourist">Xóa</button></div></div>');
            }
            lastItem.after(clone);
        });

        $(document).on('click', '.removeTourist', function (e) {
            e.preventDefault();
            $(this).closest('.touristBlock').remove();
            $('.touristBlock').each(function (index) {
                let currentIndex = $(this).attr('data-index');
                $(this).find('input').each(function () {
                    let currentName = $(this).attr('name');
                    let newName = currentName.replace(currentIndex, index);
                    $(this).attr('name', newName);
                })
            })
        })
    </script>
@endsection
