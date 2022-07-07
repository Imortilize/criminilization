$(".random").unbind().bind("click", function () {
    $(".keno-table td").attr("class", "text-center");

    for (var i = 0; i < 10; i++) {
        var element = $(".keno-table td:not(.chosen)");
        var rand = Math.round(Math.random() * element.length);
        element.eq(rand).click();
    }
});

$(".keno-table td").unbind().bind("click", function () {

    if ($(this).hasClass("chosen")) {
        $(this).removeClass("chosen")
    } else {
        if ($(".keno-table td.chosen").length < 10) {
            $(this).addClass("chosen");
            var values = [];
            $(".keno-table td.chosen").each(function () {
                values.push($(this).text().trim());
            });
            $("[name='numbers']").val(values.join(","));
        } 
    }
});