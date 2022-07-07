$(function () {
	$(".select-all").bind("click", function () {
		if ($(this).text().trim() == "Select All") {
			$(".garage-cars input[type='checkbox']").prop("checked", true);
		} else {
			$(".garage-cars input[type='checkbox']").prop("checked", false);
		}
		$(".garage-cars input[type='checkbox']").eq(0).trigger("change");
	});

	$(".garage-cars input[type='checkbox']").bind("change", function () {
		if (
			$(".garage-cars input[type='checkbox']").length == $(".garage-cars input[type='checkbox']:checked").length 
		) {	
			$(".select-all").text("Deselect All");
		} else {
			$(".select-all").text("Select All");
		}
	});

})