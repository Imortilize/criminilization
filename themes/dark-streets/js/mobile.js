$(function () {
    $(".mobile-toggle, .close-mobile-menu").unbind().bind("click", function () {
        var e = $(".mobile-menu");
        if (e.is(":visible")) {
            e.hide();
            $("body").removeClass("no-scroll");
        } else {
            e.show(300);
            $("body").addClass("no-scroll");
        }
    });
});

	$('i.fas,i.far,i.fad,i.fab,i.fal').each(function(i, obj) {
		$(this).attr("aria-hidden","true").addClass('fa-fw');
	});