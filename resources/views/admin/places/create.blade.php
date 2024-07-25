@extends('layouts.admin')

@section('admin')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Create Place</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tours.index') }}">Tour</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('itineraries.index', $itinerary->tour_id) }}">Hành trình</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('places.index', [$itinerary->tour_id, $itinerary->id]) }}">Danh sách điểm đến</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('places.store', [$itinerary->tour_id, $itinerary->id]) }}"
                      class="form-horizontal"
                      method="post"
                      enctype="multipart/form-data"
                      id="formCreatePlace">
                    @csrf
                    <div class="form-group">
                        <label for="name">
                            Tên địa điểm <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="name" id="name" placeholder=""
                               value="{{ old('name') }}">
                        @error('name')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">
                            Mô tả
                        </label>
                        <textarea name="description" id="description" cols="30"
                                  rows="10">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-info waves-effect waves-light">Thêm</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            disableSubmitButton('#formCreatePlace');

            CKEDITOR.replace('description');
        });
    </script>
@endsection
