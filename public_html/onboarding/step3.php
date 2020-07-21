<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();
if (isset($_POST['skip-btn'])) {
    Header('Location:/onboarding/step3.php');
}
if (isset($_POST['submit-btn'])) {
    //User Photo Upload
    //TODO: Impliment Cropper or similar plugin
    $photo_value = $_SESSION['photo'];
    if ($_FILES['profile_photo']['error'] == 0 && isset($_FILES['profile_photo'])) {
        $name = $_FILES['profile_photo']['name'];
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
        $target_file = $target_dir . basename($_FILES['profile_photo']['name']);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $extensions_arr = array("jpg","jpeg","png","gif");

        if(in_array($image_file_type, $extensions_arr)){
            $unlink_result=unlink($_SERVER['DOCUMENT_ROOT'] . $photo_value);
            $debug = debug($unlink_result); 
            if(move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_dir . $user_id .".". $image_file_type))
            $photo_value = "/uploads/$user_id.$image_file_type"; 
        }
        $uploadsql = "UPDATE user SET
        photo = :photo_value 
        WHERE id ={$_SESSION['id']}";
        $updatePhoto = $pdo->prepare($uploadsql);
        $updatePhoto->execute(['photo_value' => $photo_value]);
    if ($updatePhoto)  {
        
    }
    }
}
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
        <input type ="submit" class="btn btn-primary" name="sumbit-btn" value="Upload">
        <div>
            <input type ="submit" class="btn btn-primary" name="skip-btn" value="Skip">
        </div>
    </form>
{footer}
<?php ob_end_flush(); ?>