@if($day < 7)
    <span class="badge badge-pill badge-danger">{{ $day == 0 ? 'Quá hạn' : 'Sắp dễn ra' }}</span>
@elseif($day < 30)
    <span class="badge badge-pill badge-warning">Còn {{ $day }} ngày nữa</span>
@else
    <span class="badge badge-pill badge-primary">Còn {{ $day }} ngày nữa</span>
@endif
