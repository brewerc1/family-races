<?php
return <<< HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <title>$page_title</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="/css/races.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/library/jquery.multifield.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" rel="stylesheet">


    <script>
        $( document ).ready(function() {
            $javascript

            var path_id = $(location).attr('pathname').split('/')[1];
            $("#" + path_id).addClass("active");

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

                // Close main_nav when modal is active
                var trigger_id =  $('#main-navigation').attr('data-target');
                $(trigger_id).toggleClass("collapsed");
            });

        });
    </script>

</head>
<body>

<!-- Modal -->
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
</div>

HTML;
