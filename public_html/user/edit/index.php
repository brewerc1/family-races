<?php
/**
 * Page to Edit User Profile
 * 
 * This page allows the user to edit their profile data. 
 * DB is updated when the 'save' button is clicked.
 * Photo uploads are handled via AJAX independent of this form's SAVE.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// set the page title for the template
$page_title = "Edit User Profile";

// include the menu javascript for the template
$javascript =<<< JAVASCRIPT

\$image_crop = $('#croppie_element').croppie(
    {
        enableExif: true,
        viewport: 
        {
            width:200,
            height:200,
            type:'circle'
        },
        boundary:
        {
            width:300,
            height:300
        }
    }
);

$('#photo_upload_button').on(
    'change', function(){
        var reader = new FileReader();
        reader.onload = function (event) {
            \$image_crop.croppie(
                'bind', 
                {
                    url: event.target.result
                }
            ).then(function(){
                console.log('jQuery bind complete');
            });
        }
		reader.readAsDataURL(this.files[0]);
		$('.alert').alert('close');
        $('#upload_image_modal').modal('show');
    }
);

$('.crop_image').click(function(event){
    \$image_crop.croppie(
        'result', 
        {
            type: 'base64',
            size: {width: 300},
            format: 'jpeg',
            quality: 0.8,
            circle: false,
        }
    ).then(function(response){
        $('#user_profile_photo').attr("src", response);
        $.ajax(
            {
                url:"/library/photo_uploader.php",
                type: "POST",
                data:
                {
					"id": {$_SESSION['id']},
					"type": "profile",
                    "cropped_image": response
                },
                success:function(data)
                {
                    $('#upload_image_modal').modal('hide');
                    $('#photo_upload_button').val('');
					$('#ajax_alert').addClass('animate__animated animate__delay-1s animate__bounceIn').html(data);
					$('.alert').on('closed.bs.alert', function () {
						$('#ajax_alert').removeClass('animate__animated animate__delay-1s animate__bounceIn');
						$('#skip').removeClass('animate__animated animate__delay-1s animate__tada');
					});
                }
            }
        );
    })
});
/* END AJAX Photo Uploader */
JAVASCRIPT;

if (!isset($_SESSION["id"])){
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

// logged in user
$user_id = $_SESSION['id'];
$update_time_stamp = strtotime($_SESSION['update_time']); // cache busting

// State Select Array
$state_array = array(	
	"AK" => "Alaska",
	"AL" => "Alabama",
	"AR" => "Arkansas",
	"AZ" => "Arizona",
	"CA" => "California",
	"CO" => "Colorado",
	"CT" => "Connecticut",
	"DC" => "District of Columbia",
	"DE" => "Delaware",
	"FL" => "Florida",
	"GA" => "Georgia",
	"HI" => "Hawaii",
	"IA" => "Iowa",
	"ID" => "Idaho",
	"IL" => "Illinois",
	"IN" => "Indiana",
	"KS" => "Kansas",
	"KY" => "Kentucky",
	"LA" => "Louisiana",
	"MA" => "Massachusetts",
	"MD" => "Maryland",
	"ME" => "Maine",
	"MI" => "Michigan",
	"MN" => "Minnesota",
	"MO" => "Missouri",
	"MS" => "Mississippi",
	"MT" => "Montana",
	"NC" => "North Carolina",
	"ND" => "North Dakota",
	"NE" => "Nebraska",
	"NH" => "New Hampshire",
	"NJ" => "New Jersey",
	"NM" => "New Mexico",
	"NV" => "Nevada",
	"NY" => "New York",
	"OH" => "Ohio",
	"OK" => "Oklahoma",
	"OR" => "Oregon",
	"PA" => "Pennsylvania",
	"PR" => "Puerto Rico",
	"RI" => "Rhode Island",
	"SC" => "South Carolina",
	"SD" => "South Dakota",
	"TN" => "Tennessee",
	"TX" => "Texas",
	"UT" => "Utah",	
	"VA" => "Virginia",
	"VT" => "Vermont",
	"WA" => "Washington",
	"WI" => "Wisconsin",
	"WV" => "West Virginia",
	"WY" => "Wyoming"
);

// Check if "save" button was clicked
if(isset($_POST['save_button'])){

    if(empty($_POST['first_name'])){
        $first_name_value = $_SESSION['first_name']; 
    } else {
        $first_name_value = trim($_POST['first_name']);
    }

    if(empty($_POST['last_name'])){
       $last_name_value = $_SESSION['last_name'];
    } else {
        $last_name_value = trim($_POST['last_name']);
    }

    if(empty($_POST['motto'])){
        $motto_value = $_SESSION['motto'];
    } else {
        $motto_value = trim($_POST['motto']);
    }

    if(empty($_POST['email'])){
        $email_value = $_SESSION['email'];
    } else {
        $email_value = filter_var(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
    }

    if(empty($_POST['city'])){
        $city_value = $_SESSION['city'];
    } else {
        $city_value = trim($_POST['city']);
    }

    if(isset($_POST['state']) && array_key_exists($_POST['state'], $state_array)){
        $state_value = trim($_POST['state']);
    } else {
        $state_value = $_SESSION['state'];
    }

    // PDO to update the DB 
    $update_preferences_sql = 'UPDATE user SET 
		first_name = :first_name_value,
		last_name = :last_name_value,
		motto = :motto_value,
		email = :email_value,
		city = :city_value,
		state = :state_value 
    WHERE id = :user_id';

    $update_preferences_result = $pdo->prepare($update_preferences_sql);
    $update_preferences_result->execute([
		'first_name_value' => $first_name_value,
		'last_name_value' => $last_name_value,
		'motto_value' => $motto_value,
		'email_value' => $email_value,
		'city_value' => $city_value,
		'state_value' => $state_value,
		'user_id' => $user_id
    ]);
    
    //requery DB to update $_SESSION. Ensures $_SESSION is always in sync with DB.
    if ($update_preferences_result){    
        $update_session_sql = 
        "SELECT first_name, last_name, motto, email, city, state, update_time FROM user WHERE id = :user_id";
        $update_session_result = $pdo->prepare($update_session_sql);
        $update_session_result->execute(['user_id' => $user_id]);
        $row = $update_session_result->fetch();

        // Set the session variable for each column returned
        foreach( $row as $key => $value ){
          $_SESSION[$key] = $value;
        }

        $update_time_stamp = strtotime($row["update_time"]);
        header("Location: /user/");
        exit;
    }
}

///// DEBUG
//$debug = debug($_POST);
///// end DEBUG
?>
{header}
{main_nav}
    <main role="main" id="user_profile_edit_page">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <section class="form-row sticky-top" id="user_head">
                <div class="form-group col-sm-4 text-center">
                    <img class="rounded-circle" id="user_profile_photo" src="<?php echo $_SESSION['photo'] . '?' . $update_time_stamp;?>" alt="My Photo">
                    <div id="ajax_alert"></div>
				</div>
				<div class="form-row col-sm-8 justify-content-center">
					<div id="photo_upload" class="custom-file d-flex vertical-center col-sm-7">
						<input type="file" id="photo_upload_button" class="d-inline custom-file-input" accept="image/*">
						<label class="custom-file-label" for="photo_upload_button">Camera or Library</label>
					</div>
				</div>
            </section>

            <section id="user_meta">

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="motto" class="col-form-label" >First Name:</label> 
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $_SESSION['first_name'] ?>" maxlength="45">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="motto" class="col-form-label" >Last Name:</label> 
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $_SESSION['last_name'] ?>" maxlength="45">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email" class="col-form-label" >Email:</label> 
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $_SESSION['email'] ?>" maxlength="255">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="city" class="col-form-label" >City:</label> 
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo $_SESSION['city'] ?>" maxlength="45">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="state" class="col-form-label" >State:</label> 
                        <select class="form-control" id="state" name="state">
                            <?php
                            foreach ($state_array as $key => $value) {
                                if($_SESSION['state'] == $key){
                                    $state_selected_tag = 'selected';
                                } else {
                                    $state_selected_tag = '';
                                }
echo <<<ENDOPTION
                                <option value="$key" $state_selected_tag>$value</option>\n
ENDOPTION;
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label for="motto" class="col-form-label" >Motto:</label> 
                        <textarea class="form-control" id="motto" name="motto" rows="2" maxlength="255"><?php echo $_SESSION['motto'] ?></textarea>
                    </div>
                </div>

            </section><!-- END user_meta -->
            <div class="form-row my-5">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary btn col-sm-5" name="save_button">Save</button>
                    <a class="btn btn-text d-block mt-2 text-center" href="/user/">Cancel</a>
                </div>
            </div>
        </form>

        <!-- modal for photo cropping -->
        <div class="modal" id="upload_image_modal" tabindex="-1" role="dialog" aria-labelledby="croppie_modal_label" data-backdrop="static" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="croppie_modal_label">Adjust Your Photo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p><small>Drag and zoom the image to center and fill the circle with your face.</small></p>
                <div id="croppie_element"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary crop_image">Save</button>
              </div>
            </div>
          </div>
        </div>
        <!-- END: modal for photo cropping -->

    </main>
{footer}
<?php ob_end_flush(); ?>