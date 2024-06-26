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
