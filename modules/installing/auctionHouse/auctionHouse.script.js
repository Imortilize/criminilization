$(function () {
    $('[name="buyNowPrice"]').attr("disabled", "disabled")
    $('[name="buyNow"]').bind("change", function () {
        if ($(this).prop("checked")) {
            $('[name="buyNowPrice"]').removeAttr("disabled");
        } else {
            $('[name="buyNowPrice"]').attr("disabled", "disabled");
        }
    });

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
});