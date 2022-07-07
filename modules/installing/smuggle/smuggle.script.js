
$(function () {
	$("[data-owned]").click(function () {
		$(this).parent().parent().find("input").val($(this).attr("data-owned"));
	});
});