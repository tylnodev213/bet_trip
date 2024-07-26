@extends('layouts.admin')
@section('style')
    <style>
        #formCreateFAQ textarea {
            resize: none;
        }
    </style>
@endsection
@section('admin')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tạo Q&A</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tours.index') }}">Tour</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('faqs.index', $tourId) }}">Q&A</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tạo Q&A</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('faqs.store', $tourId) }}" class="form-horizontal" method="post"
                      enctype="multipart/form-data"
                      id="formCreateFAQ">
                    @csrf
                    <div class="form-group">
                        <label for="question">Câu hỏi<span class="text-danger">*</span></label>
                        <textarea type="text" class="form-control" name="question" id="question"
                                  placeholder=""
                                  rows="3">{{old('question')}}</textarea>
                        @error('question')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="answer">Đáp án <span class="text-danger">*</span></label>
                        <textarea type="text" class="form-control" name="answer" id="answer"
                                  placeholder=""
                                  rows="5">{{old('answer')}}</textarea>
                        @error('answer')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="d-flex align-items-center">
                            <input type="hidden" name="status" id="status">
                            <label for="name" class="m-t-10">Trạng thái</label>
                            <div class="m-l-10">
                                @include('components.button_switch',
                                     [
                                         'status' => empty(old('status')) ? 1 : old('status'),
                                         'id' => 'statusFAQ'
                                     ])
                            </div>
                        </div>

                        <div>
                            @error('status')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-info waves-effect waves-light">Thêm mới</button>

                </form>

            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            disableSubmitButton('#formCreateFAQ');

            $('#formCreateFAQ').submit(function (e) {
                e.preventDefault();

                if ($('#statusFAQ').is(":checked")) {
                    $('#status').val(1);
                } else {
                    $('#status').val(2);
                }

                this.submit();
            })
        });
    </script>
@endsection
