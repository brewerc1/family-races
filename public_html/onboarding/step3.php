<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
?>
{header}
{main_nav}
<h1>Your Photo</h1>
        <div>
            <img src="no-user-image.jpg">
        </div>
    <form>
        <div class="form-group">
            <input type="file" class="form-control-file" id="photoUpload" name="photoUpload" placeholder="Add a Profile Photo">
        </div>
    </form>
{footer}
<?php ob_end_flush(); ?>