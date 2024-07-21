$(document).ready(function () {
    $("#slideDestinations").owlCarousel({
        loop: false,
        margin: 30,
        responsiveClass: true,
        dots: false,
        nav: true,
        navText: [`<img src="images/icon/arrow-right.svg" alt="prev">`,
            `<img src="images/icon/arrow-right.svg" alt="next">`
        ],
        responsive: {
            0: {
                items: 2,
            },
            768: {
                items: 2,
            },
            992: {
                items: 3,
            },
            1200: {
                items: 4,
            },
        }
    });

    $("#slideTours").owlCarousel({
        loop: false,
        margin: 30,
        responsiveClass: true,
        dots: false,
        nav: true,
        navText: [`<img src="images/icon/arrow-right.svg" alt="prev">`,
            `<img src="images/icon/arrow-right.svg" alt="next">`
        ],
        responsive: {
            0: {
                items: 1,
            },
            768: {
                items: 2,
            },
            992: {
                items: 2,
            },
            1200: {
                items: 3,
            },
        }
    });


    $("#slideCultural").owlCarousel({
        loop: false,
        margin: 30,
        responsiveClass: true,
        dots: false,
        nav: true,
        navText: [`<img src="images/icon/arrow-right.svg" alt="prev">`,
            `<img src="images/icon/arrow-right.svg" alt="next">`
        ],
        responsive: {
            0: {
                items: 1,
            },
            768: {
                items: 2,
            },
            992: {
                items: 2,
            },
            1200: {
                items: 3,
            },
        }
    });

    $("#slideImageThumnail").owlCarousel({
        loop: false,
        margin: 30,
        dots: false,
        responsive: {
            0: {
                items: 3,
            },
            1200: {
                items: 4,
            },
        }
    });

    $('.next-slide').click(function (event) {
        let slideID = event.target.getAttribute('data-target');
        $(slideID).trigger('next.owl.carousel');
    })

    // Change icon nav-header

    // left: 37, up: 38, right: 39, down: 40,
    // spacebar: 32, pageup: 33, pagedown: 34, end: 35, home: 36
    var keys = {37: 1, 38: 1, 39: 1, 40: 1};

    function preventDefault(e) {
        e.preventDefault();
    }

    function preventDefaultForScrollKeys(e) {
        if (keys[e.keyCode]) {
            preventDefault(e);
            return false;
        }
    }

    // modern Chrome requires { passive: false } when adding event
    var supportsPassive = false;
    try {
        window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
            get: function () {
                supportsPassive = true;
            }
        }));
    } catch (e) {
    }

    var wheelOpt = supportsPassive ? {passive: false} : false;
    var wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';

    // call this to Disable
    function disableScroll() {
        window.addEventListener('DOMMouseScroll', preventDefault, false); // older FF
        window.addEventListener(wheelEvent, preventDefault, wheelOpt); // modern desktop
        window.addEventListener('touchmove', preventDefault, wheelOpt); // mobile
        window.addEventListener('keydown', preventDefaultForScrollKeys, false);
    }

    // call this to Enable
    function enableScroll() {
        window.removeEventListener('DOMMouseScroll', preventDefault, false);
        window.removeEventListener(wheelEvent, preventDefault, wheelOpt);
        window.removeEventListener('touchmove', preventDefault, wheelOpt);
        window.removeEventListener('keydown', preventDefaultForScrollKeys, false);
    }

    $('#navbarBtn').on('click', function () {
        $('#navbarBtn').toggleClass('navbar-active-btn');
        if ($('#navbarBtn').hasClass('navbar-active-btn')) {
            disableScroll();
        } else {
            enableScroll();
        }
    });

    // Chang icon Filter - List Tour Page
    $('#btnFilterTours').on('click', function () {
        $('.iconBtnFilter').toggleClass('d-none');
    });

    // Clear form filter
    $('#clearFormFilter').on('click', function () {
        $('#formSelectFilter')[0].reset();
    });

    // Choose thumbnail image
    $('.thumbnailItem').on('click', function (e) {
        $('.thumbnailItem').removeClass("target");
        e.target.classList.add("target");
        linkSrc = e.target.getAttribute('src');
        $('#mainImageTour').attr('src', linkSrc);
    });

    // Rate review{
    $('.rate-star').hover(function (e) {
        let currentRate = e.target.dataset.rate;
        $('#inputRateReview').val(currentRate);
        $('#rateReview').children().each(function () {
            if (this.dataset.rate <= currentRate) {
                this.classList.remove('bi-star');
                this.classList.add('bi-star-fill');
            } else {
                this.classList.remove('bi-star-fill');
                this.classList.add('bi-star');
            }
        });
    }, function () {
        // out
    });

    // Check has page review
    let url = new URL(window.location.href);
    if (url.searchParams.get('page')) {
        $('#pills-review-tab').trigger('click');
        if ($('#pills-review-tab').length) {
            document.getElementById("pills-review-tab").scrollIntoView();
        }
    }

    //Panorama
    if ($('#imagePanoramic').length > 0) {
        pannellum.viewer('imagePanoramic', {
            "type": "equirectangular",
            "panorama": "./images/travel-360.jpg",
            "autoLoad": true,
        });
    }

    $('#imagePanoramic').on('click', function () {
        $('.wrap-panoramic').hide();
    });

    //Video
    $('#videoTour, .wrap-video').on('click', function () {
        let video = $('#videoTour').get(0);
        if (video.paused) {
            video.play();
            $('#iconPlayVideo').hide();
            $('#iconPauseVideo').show();
            $('.wrap-video').hide();
        }
    });

    // Departure time picker
    $('#departureTime').on('target', function () {
        $('#departureTimePicker').trigger('click');
    });

    let durationDay = $('#duration').val();
    let timeDeparutre = Date.parse($('#inputDepartureTime').val());
    if (isNaN(timeDeparutre)) {
        timeDeparutre = Date.now();
    }

    let startDepartureDate = new Date(timeDeparutre);
    let endDepartureDate = new Date(timeDeparutre + durationDay * 24 * 60 * 60 * 1000);
    $('#departureTime').val(startDepartureDate.toLocaleDateString("en-US") + ' - ' + endDepartureDate.toLocaleDateString("en-US"));

    $('#departureTimePicker').daterangepicker({
        singleDatePicker: true,
        startDate: startDepartureDate,
        locale: {
            format: 'MM/DD/YYYY',
        },
        minDate: moment().add(1, 'days'),
    }, function (start, end) {
        $('#inputDepartureTime').val(start.format("YYYY-MM-DD"));

        end.add(durationDay - 1, 'days');
        $('#departureTime').val(start.format("M/D/YYYY") + ' - ' + end.format("M/D/YYYY"));
        checkRoom(start.format("YYYY-MM-DD"));
    });

    // if ($('#departureTimePicker').length) {
    //     checkRoom($('#departureTimePicker').data('daterangepicker').startDate.format("YYYY-MM-DD"));
    // }

    // Check room
    function checkRoom(date) {
        let linkCheckRoom = $('#linkCheckRoom').val();
        $.ajax({
            url: linkCheckRoom,
            method: "GET",
            data: {departure_time: date},
            success: function (response) {
                for (const [key, value] of Object.entries(response.room_available)) {
                    $('#roomAvailable' + key).text(value);
                    $('#numberRoom' + key).prop('max', value);
                }
            }
        });
    }

    // Calculate Price
    let PRICE_CHILD_DEFAULT = $('#price_child').val();
    let PRICE_ADULT_DEFAULT = $('#price_adult').val();
    $('#selectNumberAdults, #selectNumberChildren').on('change', function () {
        caculatePrice();
    });

    $('.numberRoom').on('keyup', function () {
        if ($(this).val() < 0) {
            $(this).val(0);
        }
        caculatePrice();
    });

    function caculatePrice() {
        let numberAdults = $('#selectNumberAdults').val();
        let numberChildren = $('#selectNumberChildren').val();
        let discount = $('#discountCoupon').val();
        let price = parseInt(numberAdults) * PRICE_ADULT_DEFAULT + parseInt(numberChildren) * PRICE_CHILD_DEFAULT;
        if (isNaN(price)) {
            $('#totalPrice').text('VNĐ');

            return;
        }
        let priceRoom = 0;
        $('.numberRoom').each(function (index) {
            if ($(this).val() > 0) {
                priceRoom += $(this).data('price') * $(this).val();
            }
        });

        price = price + priceRoom;
        let total = price - price * discount / 100;
        $('#totalPrice').text(total.toLocaleString() + ' VNĐ');

        $('#priceAfterDiscount').text(price.toLocaleString() + ' VNĐ - ' + discount + '%');
        if (discount > 0) {
            $('#priceAfterDiscount').removeClass('d-none');
        }
    }

    caculatePrice();

    //Apply coupon
    $('#btnCouponSubmit').on('click', function (e) {
        $(this).prop('disabled', true);
        let code = $('#coupon').val();
        if (code === "") {
            toastr.error("Không được để trống mã giảm giá");
            $('#btnCouponSubmit').prop('disabled', false);
            return;
        }

        $.ajax({
            url: $('#linkCheckCoupon').val(),
            type: 'get',
            data: {code: code},
            success: function (response) {
                $('#discountCoupon').val(response.discount);
                $('#codeCoupon').val(response.code);
                caculatePrice();
                toastr.success("Áp dụng mã giảm giá thành công");
            },
            error: function (jqXHR) {
                let response = jqXHR.responseJSON;
                toastr.error(response.message);
            },
            complete: function () {
                $('#btnCouponSubmit').prop('disabled', false);
            }
        });
    });

    //Function validate
    function stringContainsNumber(_string) {
        return /\d/.test(_string);
    }

    function stringOnlyNumber(_string) {
        return /^\d+$/.test(_string);
    }

    function checkMail(_string) {
        return /^[a-z][a-z0-9_\.]{2,32}@[a-z0-9]{2,}(\.[a-z0-9]{2,4}){1,2}/.test(_string);
    }

    function removeAllWhiteSpace(_string) {
        return _string.replace(/\s+/g, '')
    }

    function checkPhone(_string) {
        _string = _string.replace(/\s+/g, '');
        // Mobile
        if (/^(0|\+84)[35789]([0-9]{8})$/.test(_string)) {
            return true;
        }

        return /^(0|\+84)2([0-9]{9})$/.test(_string)
    }

    function escapeHTML(str) {
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function getValue(_selector) {
        return escapeHTML(($.trim($(_selector).val())));
    }
});
