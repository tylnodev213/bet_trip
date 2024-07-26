@extends('layouts.client')
@section('content')
    <!-------------------- Breadcrumb -------------------->
    <div class="breadcrumb-wrap">
        <div class="container">
            <nav style="--bs-breadcrumb-divider: ''" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="#">Tours</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <!-------------------- End Breadcrumb -------------------->

    <!-------------------- Filter -------------------->
    <div class="box-slide slide-tour list-tours container">
        <div class="header-slide d-flex align-items-end">
            <p class="title-slide">Danh sách tour</p>
            <div class="btn-filter-wrap">
                <button class="btn btn-outline btn-filter d-flex align-items-center justify-content-between"
                        id="btnFilterTours" data-bs-toggle="collapse" data-bs-target="#filterCollapse"
                        aria-expanded="false" aria-controls="filterCollapse">
                    <span>Lọc</span>
                    <i class="fa fa-x d-none iconBtnFilter"></i>
                    <i class="fa fa-chevron-down iconBtnFilter"></i>
                </button>
                <!-- Collapse Filter -->
                <div class="collapse collapse-fillter" id="filterCollapse" style="height: 990px !important;">
                    <div class="card card-body">
                        <form action="" id="formSelectFilter">
                            <div class="filter-header d-flex justify-content-between align-items-center">
                                <p>Lọc bởi</p>
                                <span class="text-clear" id="clearFormFilter">Xóa</span>
                            </div>
                            <div class="budget-bar">
                                <h5>Số tiền:</h5>
                                <div id="sliderRangePrice">
                                    <div slider id="slider-distance">
                                        <div>
                                            <div inverse-left></div>
                                            <div inverse-right></div>
                                            <div range></div>
                                            <span thumb></span>
                                            <span thumb></span>
                                            <div sign>
                                                <span></span>đ
                                            </div>
                                            <div sign>
                                                <span></span>đ
                                            </div>
                                        </div>
                                        <input type="range" tabindex="0"
                                               value="{{ request()->min_price ?? 3000000 }}"
                                               max="10000000" min="0" step="100000"
                                               oninput="leftRange(this)" name="min_price" id="minPrice"/>

                                        <input type="range" tabindex="0"
                                               value="{{ request()->max_price ?? 6000000}}"
                                               max="10000000" min="0" step="100000"
                                               oninput="rightRange(this)" name="max_price" id="maxPrice"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-select-filter">
                                <div class="form-group">
                                    <hr>
                                    <h5>Tour</h5>
                                    <input type="text" class="form-control" name="tour_name" placeholder="Tên tour"
                                           value="{{ request()->tour_name }}">
                                </div>

                                <div class="form-group">
                                    <hr>
                                    <h5>Điểm đến</h5>
                                    <input class="form-control" type="text" name="destination_name"
                                           placeholder="Tên điểm đến"
                                           value="{{ request()->destination_name }}">
                                </div>

                                <div class="form-group">
                                    <hr>
                                    <h5>Thời gian</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="filter_duration[]"
                                               value="1"
                                               id="duration1" {{ in_array(1, $filterDuration) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="duration1">
                                            0-3 ngày
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="filter_duration[]"
                                               value="2" id="duration2"
                                               id="duration4"{{ in_array(2, $filterDuration) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="duration2">
                                            3-5 ngày
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="filter_duration[]"
                                               value="3" id="duration3"
                                               id="duration4"{{ in_array(3, $filterDuration) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="duration3">
                                            5-7 ngày
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="filter_duration[]"
                                               value="4"
                                               id="duration4"{{ in_array(4, $filterDuration) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="duration4">
                                            trên 1 tuần
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <hr>
                                    <h5>Thể loại</h5>
                                    @foreach($types as $type)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="filter_type[]"
                                                   value="{{ $type->id }}"
                                                   id="type{{ $type->id }}" {{ in_array($type->id, $filterType   ) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="type{{ $type->id }}">
                                                {{ $type->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <button class="btn btn-primary w-100 btn-submit-filter">
                                    Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End Collapse Filter -->
            </div>
        </div>
    </div>
    <!-------------------- End Filter -------------------->

    <!-------------------- List Tours -------------------->
    <div class="tours container mt-5">
        <div class="row">
            @if(!empty($tours) && $tours->isNotEmpty())
                @foreach ($tours as $tour)
                    <div class="col-12 col-lg-4 mb-5">
                        <div class="card">
                            <a href="{{ route('client.tours.detail', $tour->slug) }}" class="tour-image">
                                <img class="card-img-top" src="{{ asset('storage/images/tours/' . $tour->image) }}"
                                     alt="{{ $tour->name }}">
                                <div class="best-seller {{ $tour->trending === 1 ? '' : 'd-none'  }}">
                                    <span>Nổi bật</span>
                                </div>
                            </a>
                            <div class="card-body">
                                <a href="{{ route('client.tours.detail', $tour->slug) }}" class="tour-title">
                                    <h5 class="card-title">{{ $tour->name }}</h5>
                                </a>
                                <div class="d-flex justify-content-between pt-3">
                                    <span><i
                                            class="fa-regular fa-clock tour-duration"></i> <span>{{ \App\Libraries\Utilities::durationToString($tour->duration) }}</span></span>
                                    <span class="price-discount">{{ number_format($tour->price_child * 1.2) }}đ</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p>
                                        @php
                                            $rateReview = \App\Libraries\Utilities::calculatorRateReView($tour->reviews);
                                        @endphp
                                        @include('components.rate_review', ['rate' => $rateReview['total']])
                                        <br/><small>Có {{ $tour->booking_count }} lượt đặt</small>
                                    </p>
                                    <p class="price">{{ number_format($tour->price_child) }}đ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center w-100">{{ config('config.no_data_search') }}</p>
            @endif

        </div>
    </div>
    <!-------------------- End List Tours-------------------->

    <div class="container">
        <div class="pagination-tours d-flex justify-content-end align-items-baseline w-100">
            {!! $tours->links('components.pagination') !!}
        </div>
    </div>
@endsection


