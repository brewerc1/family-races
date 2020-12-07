<?php

global $meta;

if(empty($meta)){
	$meta = '';
}

return <<< HTML
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
	<meta name="robots" content="noindex">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=2">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=2">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=2">
	<link rel="manifest" href="/site.webmanifest?v=2">
	<link rel="mask-icon" href="/safari-pinned-tab.svg?v=2" color="#373535">
	<link rel="shortcut icon" href="/favicon.ico?v=2">
	<meta name="apple-mobile-web-app-title" content="Keene Challenge">
	<meta name="application-name" content="Keene Challenge">
	<meta name="msapplication-TileColor" content="#2d89ef">
	<meta name="theme-color" content="#ffffff">
	<title>$page_title</title>
	$meta
	<link href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" rel="stylesheet">
	<link href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" rel="stylesheet">
	<link href="/css/bootstrap.min-lux.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css" integrity="sha512-rxThY3LYIfYsVCWPCW9dB0k+e3RZB39f23ylUYTEuZMDrN/vRqLdaCBo/FbvVT6uC2r0ObfPzotsfKF9Qc5W5g==" crossorigin="anonymous" />
	<link href="/css/races.css" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="/library/jquery.multifield.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>

	<script>
		$( window ).on( "load", function() {

			// Injected javascript from PHP
	        $javascript
			
			// Main Nav: Highlight element based on page path
	        var path_id = $(location).attr('pathname').split('/')[1];
	        $("#" + path_id).addClass("active");

			// Main modal
	        $('#mainModal').on('show.bs.modal', function (event) {
	            // Link or Button that triggered the modal
	            var button = $(event.relatedTarget);

	            // Extract info from data-* attributes
	            var title = button.data('title');
	            var message = button.data('message');
	            var button_primary_text = button.data('button-primary-text');
	            var button_primary_action = button.data('button-primary-action');
	            var button_secondary_text = button.data('button-secondary-text');
	            var button_secondary_action = button.data('button-secondary-action');

	            var modal = $(this);
	            modal.find('.modal-title').text(title);
	            modal.find('#message').html(message);
	            modal.find('.btn.btn-primary').html(button_primary_text);
	            modal.find('.btn.btn-primary').attr("onclick",button_primary_action);
	            modal.find('.btn.btn-secondary').html(button_secondary_text);
	            modal.find('.btn.btn-secondary').attr("onclick",button_secondary_action);

				// Close main_nav when modal is active (on mobile devices)
	            var trigger_id =	$('#main-navigation').attr('data-target');
	            $(trigger_id).toggleClass("collapsed");
	        });
    	});
	</script>
</head>
<body>
	<!-- #mainModal -->
	<div class="modal fade" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel" data-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="mainModalLabel">Modal Title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="message"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" onclick="" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="">Submit</button>
				</div>
			</div>
		</div>
	</div><!-- end #mainModal -->
HTML;
