@if($status == BOOKING_NEW)
    <span class="badge badge-pill badge-success">Mới</span>
    @include('components.countdown_booking', ['day' => $day])
@elseif($status == BOOKING_CONFIRM)
    <span class="badge badge-pill badge-info">Đã xác nhận</span>
@elseif($status == BOOKING_COMPLETE)
    <span class="badge badge-pill badge-success">Hoàn thành</span>
@elseif($status == BOOKING_CANCEL)
    <span class="badge badge-pill badge-danger">Đã Hủy</span>
@endif
