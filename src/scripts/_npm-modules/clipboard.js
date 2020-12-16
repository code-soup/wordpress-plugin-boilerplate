import ClipboardJS from "clipboard";

/**
 * Utilize ClipboardJS so the user can copy system report
 */
let elID = '#cs_wppb_copy_report';
if ($(elID).length) {
	let clipboard = new ClipboardJS('#cs_wppb_copy_report');
	clipboard.on('success', function (e) {
		console.log('System report copied succesfully');
		setTimeout(function () { jQuery('#cs_wppb_copy_success').attr('style', 'display:inline-block !important;') }, 300);
		setTimeout(function () { jQuery('#cs_wppb_copy_success').attr('style', 'display:none !important;') }, 5000);
		e.clearSelection();
	});
	clipboard.on('error', function (e) {
		console.log(e);
		jQuery('#cs_wppb_copy_error_message').attr('style', 'display:inline-block !important;');
	});
}