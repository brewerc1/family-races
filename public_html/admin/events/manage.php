<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

$page_title = "Create Event";
$javascript = "
$('#example-6').multifield({
        section: '.group',
        btnAdd:'#btnAdd-6',
        btnRemove:'.btnRemove',
    });
";

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}
$debug = debug();

?>
{header}
{main_nav}
<form method="post">
    <div id="example-6" class="content">
        <div class="row">
            <div class="col-md-12"><button type="button" id="btnAdd-6" class="btn btn-primary">Add Employee</button></div>
        </div>
        <div class="row group">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Name<input class="form-control" class="form-control" type="text" name="user_name[]"></label>
                </div>
            </div>
            <div class="col-md-2">
                <label>Gender
                    <select name="user_gender[]" class="form-control">
                        <option value="">Please select..</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </label>
            </div>
            <div class="col-md-4">
                <div class="col-md-2">
                    <div class="radio">
                        <label><input type="radio" name="user_role[0]" value="manager"> Manager </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="user_role[0]" value="editor"> Editor </label>
                    </div>
                    <div class="radio">
                        <label class="checkbox-inline"><input type="radio" name="user_role[0]" value="writer"> Writer </label>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger btnRemove">Remove</button>
            </div>
        </div>
    </div>
</form>

{footer}
<?php ob_end_flush(); ?>
