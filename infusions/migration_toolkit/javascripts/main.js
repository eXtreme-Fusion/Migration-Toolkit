$(document).ready(function() {
	$(".valid, .error, .info, .status").each(function () {
		var GetStatus = $(this).attr("class");
		$(this).addClass("close-" +GetStatus);
		$(".close-" +GetStatus).click(function () {
			$(this).fadeOut("slow");
		});
	});

	$('#refresh').click(function() {
		window.location.reload();
	});

	/** Team section **/
	$('.tab-click').click(function() {
		var id = this.id;
		$object = $('#tab-'+id);
		if ($object.is(':hidden')) {
			$object.fadeIn();
		} else {
			$object.fadeOut();
		}
	});
});
