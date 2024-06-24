function showLoading() {
    $.LoadingOverlay("show", {zIndex: 999999999});
}

function hideLoading() {
    $.LoadingOverlay("hide");
}

$('button[type=submit]').on('click' ,function () {
    showLoading();
    $(this).closest('form').submit();
});
