@for($i = 0; $i < 5; $i++)
    @if($i + 1 <= $rate)
        <i class="fa fa-star fill-yellow"></i>
    @else
        <i class="fa fa-star"></i>
    @endif
@endfor
