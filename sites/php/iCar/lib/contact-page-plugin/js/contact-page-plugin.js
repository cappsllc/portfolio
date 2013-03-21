jQuery(function($) {$(document).ready(function() {
	$("#ithemes-contact-page-send").click(
		function(e) {
			$("#ithemes-contact-page-form").append('<input type="hidden" name="ithemes-contact-page-' + hex_md5($("#ithemes-contact-page-name").attr("value")) + '" value="' + hex_md5(hex_md5($("#ithemes-contact-page-name").attr("value")) + hex_md5($("#ithemes-contact-page-email").attr("value")) + hex_md5($("#ithemes-contact-page-website").attr("value"))) + '" id="ithemes-contact-page-val" />');
		}
	);
});});