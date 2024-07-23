@if($status == 1)
    <span class="badge badge-pill badge-info">Xác nhận</span>
@elseif($status == 2)
    <span class="badge badge-pill badge-success">Hoàn thành</span>
@elseif($status == 3)
    <span class="badge badge-pill badge-danger">Đã Hủy</span>
@endif
