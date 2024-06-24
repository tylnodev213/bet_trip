<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GoodTrip')</title>
    <meta name="description" content="@yield('description', 'Tour and Travel')">
    <meta name="author" content="tuyenpham">
    <meta property="og:title" content="@yield('title', 'VN Travel')">
    <meta property="og:description" content="@yield('description', 'Tour and Travel')">
    <meta property="og:url" content="@yield('url', route('index'))">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="VN">
    <meta property="og:image" content="@yield('image_seo')">
    <meta property="og:image:secure_url" content="@yield('image_seo')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Font -->
    <link rel="stylesheet" href="{{ asset('font/fontawesome-free-6.1.1-web/css/all.min.css') }}">

    <!-- cdn bootstrap -->
    {{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" --}}
    {{--          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> --}}
    {{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css"> --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap-reboot.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2/select2.min.css') }}">
    {{--    <link rel="stylesheet" href="{{ asset('css/main.css') }}"> --}}
    <script src="{{ asset('js/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/select2/select2.min.js') }}"></script>

    <!-- owl carousel -->
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.css') }}">
    <!-- date-range-picker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- pannellum -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <!-- toastr -->
    <link rel="stylesheet" href="{{ asset('css/toastr.css') }}" />
    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    @yield('css')
</head>

<body>

    <!-------------------- Header -------------------->
    @include('components.header')

    <!-------------------- Content -------------------->
    @yield('content')

    <!-------------------- Footer -------------------->
    @include('components.footer')
</body>

<!-- cdn bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- cdn jquery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- owl carousel -->
<script src="{{ asset('js/owl.carousel.js') }}"></script>
<!-- date-ranger-picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- panellum -->
<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<!-- toastr -->
<script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
<!-- custom js -->
<script src="{{ asset('js/script.js') . '?' . time() }}" type="text/javascript"></script>
<script src="{{ asset('js/slider.js') }}" type="text/javascript">

<script src="{{ asset('js/loadingoverlay.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/loadingoverlay_progress.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/common.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });

    $(document).ajaxStart(function () {
        showLoading();
    });

    $(document).ajaxSuccess(function () {
        hideLoading();
    });

    $(document).ajaxError(function () {
        hideLoading();
    });

    $(document).ajaxComplete(function () {
        hideLoading();
    });

    function toastrMessage(type, message) {
        switch (type) {
            case 'info':
                toastr.info(message);
                break;
            case 'success':
                toastr.success(message);
                break;
            case 'warning':
                toastr.warning(message);
                break;
            case 'error':
                toastr.error(message);
                break;
        }
    }

    function disableSubmitButton(idForm) {
        $(idForm).submit(function() {
            $(this).find("button[type='submit']").prop('disabled', true);
        });
    }

    function enableSubmitButton(idForm, delay = 0) {
        setTimeout(function() {
            $(idForm).find("button[type='submit']").prop('disabled', false);
        }, delay);
    }

    @if (Session::has('message'))
        let type = "{{ Session::get('alert-type', 'info') }}";
        let message = "{{ Session::get('message') }}";
        toastrMessage(type, message);
    @endif
</script>
@yield('js')

</html>
