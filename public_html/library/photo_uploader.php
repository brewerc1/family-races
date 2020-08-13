<?php
/**
 * Photo Upload AJAX Handler
 * 
 * This script handles the upload of a user photo, including deleting the old
 * profile photo, writing the new profile photo, updating the DB, and updating
 * the session variables.
 * It return HTML responses on sucess and fail conditions.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

if( isset($_SESSION['id']) && isset($_POST['id']) && isset($_POST['cropped_image']) && isset($_POST['type'])) {

	$entity_id = $_POST['id'];
	
	if($_POST['type'] == 'hof'){
		$current_photo = "/uploads/{$_POST['id']}-champion.jpg";
		$file_to_write = "{$_POST['id']}-champion";
		$update_photo_sql = 'UPDATE event SET champion_photo = :photo_value, update_time = NULL WHERE id = :entity_id';
	}elseif ($_POST['type'] == 'profile' && $_POST['id'] == $_SESSION['id']){
		$current_photo = $_SESSION['photo'];
		$file_to_write = "{$_POST['id']}";
		$update_photo_sql = 'UPDATE user SET photo = :photo_value, update_time = NULL WHERE id = :entity_id';
	}else{
		echo <<< ALERT
		<div id="photo_ajax_message_wrapper" class="floating-alert alert alert-warning alert-dismissible fade show" role="alert">
		<span id="photo_ajax_message">Trying something, eh?</span>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		</div>
ALERT;
	}

    $image_file_types = array(
		'jpg' => 'jpg',
		'jpeg' => 'jpg',
		'png' => 'png',
		'gif' => 'gif'
    );

    // Separate the new base64 URI into an array, where key 0 contains the scheme, media type, and base64 extension
    // and key 1 contains the base64 encoded image data.
    $base64_parts = explode(',',$_POST['cropped_image']);

    if( preg_match('/^data:image\/(\w+);base64/', $base64_parts[0], $mime_type_array) ){ // we have a valid base64 image
        // extract the file extension from the mime type of the base64-encoded image
        $image_type = strtolower($mime_type_array[1]);

        // determine if this is a valid extension
        if (!array_key_exists($image_type, $image_file_types)) {
            throw new \Exception('Invalid image type.');
        }

        // decode base64 image to binary
        $img_data = base64_decode($base64_parts[1]);
        if ($img_data === false) {
            throw new \Exception('Base64_decode failed.');
		}

        // Delete prior photo from server
        if(isset($current_photo) && !is_null($current_photo)){
            if($current_photo != '/images/no-user-image.jpg'){
                $unlink_result = unlink( $_SERVER['DOCUMENT_ROOT'] . $current_photo );
            }
        }
    
        // Write the new image to the filesystem
        $image_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads';
        if(file_put_contents("$image_dir/$file_to_write.{$image_file_types[$image_type]}", $img_data)){
            $photo_value = '/' . basename($image_dir) . "/$file_to_write.{$image_file_types[$image_type]}";
        } else {
            throw new \Exception('Could not write the image to the filesystem.');
        }

        // Write the new image to the database. Must set update_time to NULL to trigger onUpdate CURRENT_TIMESTAMP
        // in the case that the image name hasn't changed.
        //$update_photo_sql = 'UPDATE user SET photo = :photo_value, update_time = NULL WHERE id = :user_id';
        $update_photo_result = $pdo->prepare($update_photo_sql);
        $update_photo_result->execute( ['photo_value' => $photo_value, 'entity_id' => $entity_id] );
    
        //requery DB to update $_SESSION. Ensures $_SESSION is always in sync with DB.
        if ($update_photo_result){
			if ($_POST['type'] == 'profile'){
	            $update_session_sql =  'SELECT photo, update_time FROM user WHERE id = :entity_id';
	            $update_session_result = $pdo->prepare($update_session_sql);
	            $update_session_result->execute(['entity_id' => $entity_id]);
				$row = $update_session_result->fetch();
				$_SESSION['photo'] = $row['photo'];
				$_SESSION['update_time'] = $row['update_time'];
			}
        } else {
            throw new \Exception('Unable to update session with new values.');
        }
    } else {
        throw new \Exception('Could not parse URI to extract image data');
    }
    // Looks like we made it.
    echo <<< ALERT
<div id="photo_ajax_message_wrapper" class="floating-alert alert alert-success alert-dismissible fade show" role="alert">
<span id="photo_ajax_message">Photo saved.</span>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
</div>
ALERT;
} else {
    echo <<< ALERT
<div id="photo_ajax_message_wrapper" class="floating-alert alert alert-warning alert-dismissible fade show" role="alert">
<span id="photo_ajax_message">Unrecoverable error.</span>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
</div>
ALERT;
}
