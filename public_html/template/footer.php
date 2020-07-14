<?php
global $debug, $alert_style, $notification;
$output = '';

if(isset($debug)) {
    $output .= $debug;
}

if(isset($notification) && $notification != ''){
$output .= <<< ALERT
    <div class="floating-alert alert $alert_style alert-dismissible fade show fixed-top mt-5 mx-4" role="alert">
        $notification
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
ALERT;
}

return $output .= <<< HTML
    <footer></footer>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>
HTML;
