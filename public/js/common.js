function showLoading() {
    $('.preloader').show();
}

function hideLoading() {
    $('.preloader').hide();
}

hideLoading();

$('button[type=submit]').on('click' ,function () {
    showLoading();
    $(this).closest('form').submit();
});

$(window).bind("pageshow", function(event) {
    let isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
    if (isOpera) {
        $('.preloader').hide();
    }
});
