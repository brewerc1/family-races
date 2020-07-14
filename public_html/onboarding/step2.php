<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
?>
{header}
{main_nav}
<h1>Your Profile</h1>


<form>
                <div class="form-group">
                    <input  type="text" class= "form-control" id="FN" name="FN"  placeholder="First Name"></input>
                </div>
                <div class="form-group">
                    <input  type="text" class= "form-control" id="LN" name="LN"  placeholder="Last Name"></input>
                </div>
                <div class="form-group">
                    <input  type="text"  class= "form-control" id="city" name="city" placeholder="City"></input>
                </div>
                <div class="form-group">
                    <input  type="text" class= "form-control" id="state" name="state" placeholder="State"></input>
                </div>
                <div class="form-group">
                    <input  type="text" class= "form-control" id="motto" name="motto" placeholder="Motto"></input>
                </div>
                    <input type="submit" name="next-btn" value="Next"></input>
    </form>
    {footer}
<?php ob_end_flush(); ?>
<?php
if (isset($_POST['next-btn'])) {
    $FN = $_POST['FN'];
    $LN = $_POST['LN'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $motto = $_POST['motto'];

}
?>