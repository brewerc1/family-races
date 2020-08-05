<?php
/**
 * Page to display Hall of Fame
 * 
 * This page displays the Hall of Fame for the current event, and all prior events.
 * Logged in users view this page.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// Test for authorized user
if (empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
}

// Set the page title for the template
$page_title = "Hall of Fame";

$javascript = '';

//$debug = debug("");

// Gather data for this page
// SQL to retrieve Hall of Fame related data
$hof_sql = 'SELECT event.*, user.first_name, user.last_name, user.photo, user.update_time as user_update_time
FROM event, user
WHERE event.champion_id = user.id AND event.status = 1 ORDER BY event.id DESC';
$hof_result = $pdo->prepare($hof_sql);
$hof_result->execute();
$num_hof_results = $hof_result->rowCount();

$user_id = $_SESSION['id'];
?>
{header}
{main_nav}
    <main role="main" id="hof_page">
        <h1 class="mb-5 sticky-top">Hall of Fame</h1>
		<section id="current_champion">

        <?php if($_SESSION['admin']) {
echo <<< HERE
	<form action="{$_SERVER['PHP_SELF']}" method="post" enctype="multipart/form-data">

HERE;
        }
            if($num_hof_results > 0){

                $current_champ_row = $hof_result->fetch();
                    // $current_champ_row['champion_photo'] = ""; // test for no champ_photo
                $num_hof_results -= 1;

                $event_id = $current_champ_row['id'];

                // avoid having no image to display
                if ($current_champ_row['champion_photo'] == NULL){
                    $update_time_stamp = $current_champ_row['user_update_time'];
                    $current_champ_photo = $current_champ_row['photo']."?".$update_time_stamp;
                    // avoids unlinking a user's placeholder HOF image (their profile image)
                    $champ_photo_nostamp = NULL;
                } else {
                    $event_update_time_stamp = strtotime($current_champ_row['update_time']); // cache busting
                    $current_champ_photo = $current_champ_row['champion_photo']."?".$event_update_time_stamp;
                    $champ_photo_nostamp = $current_champ_row['champion_photo'];
                }

                if ($_SESSION['admin']){
$photo_upload_div =<<< ENDDIV
						<div id="photo_upload" class="form-group">
							<input type="file" id="champion_photo_upload" class="d-inline form-control-file" accept="image/*">
						</div>
ENDDIV;
                } else {
                    $photo_upload_div = '';
                }

echo <<< ENDCURRENT
                <div class="card text-center">
                    <h2 class="card-header">Current Champion</h2>
                    <h3 class="card-title champion mt-5 mb-4">{$current_champ_row['name']}: <span class="winner_name text-muted">{$current_champ_row['first_name']}&nbsp;{$current_champ_row['last_name']}</span> <span class="badge badge-success badge-pill ml-3">\${$current_champ_row['champion_purse']}</span></h3>
                    <div class="card-body p-0">
                        <a href="/user/?u={$current_champ_row['champion_id']}">
                            <img class="w-100" id="current_champion_photo" src="{$current_champ_photo}" alt="Photo of HOF winner {$current_champ_row['first_name']} {$current_champ_row['last_name']}">
                        </a>
                        <div id="ajax_alert"></div>
{$photo_upload_div}
                    </div>
                </div>
ENDCURRENT;
                } else {
echo <<< ENDNORESULT
	<li class="list-group-item">
        <h2 class="card-header">Current Champion</h2>
        <div class="card text-center">
            <h5 class="card-title">No Current Champion</h5>
        </div>
    </li>
ENDNORESULT;
                    }
			if($_SESSION['admin']) echo "\n\t\t\t</form>";
?>

        </section> <!-- END current_champion -->

		<section id="prior_champions" class="mt-5">
			<div class="card text-center">
				<h2 class="card-header">Prior Champions</h2>
            <?php
            if ($num_hof_results > 0){
                while ($row = $hof_result->fetch()){                         
                    // avoid having no image to display

                    // $row['champion_photo'] = ""; // test for no champ_photo
                    if ($row['champion_photo'] == NULL){
                        $update_time_stamp = $row['user_update_time'];
                        $champ_photo = $row['photo']."?".$update_time_stamp;
                    } else {
                        $event_update_time_stamp = strtotime($current_champ_row['update_time']); // cache busting
                        $champ_photo = $row['champion_photo']."?".$event_update_time_stamp;                               
                    }
echo <<< ENDPREVIOUS
					<div class="card text-center">
						<h3 class="card-title champion mt-5 mb-4">{$row['name']}: <span class="winner_name text-muted">{$row['first_name']}&nbsp;{$row['last_name']}</span>
						<span class="badge badge-success badge-pill ml-3">\${$row['champion_purse']}</span></h3>
						<div class="card-body p-0">
							<a href="/user/?u={$row['champion_id']}">
								<img class="w-100" src="{$champ_photo}" alt="Photo of HOF winner {$row['first_name']} {$row['last_name']}">
							</a>
						</div>
					</div>

ENDPREVIOUS;
				}
			} else {
echo <<< ENDNORESULT
					<div class="card text-center">
						<h3 class="card-title">No Previous Champions</h3>
					</div>

ENDNORESULT;
			}?>
			</div>
		</section>

        <!-- modal for photo cropping -->
		<div class="modal" id="uploadimageModal" tabindex="-1" role="dialog" aria-labelledby="croppieModalLabel" data-backdrop="static" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title" id="croppieModalLabel">Adjust the Photo</h3>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<p>Drag the image to center the image in the square. Zoom in to fill the square and crop the image. Save the image when you're satisfied.</p>
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
	
	<script>
	    // Include the photo upload javascript
	    $image_crop = $('#croppie_element').croppie(
	        {
	            enableExif: true,
	            viewport: 
	            {
	                width:400,
	                height:400,
	                type:'square'
	            },
	            boundary:
	            {
	                width:450,
	                height:450
	            }
	        }
	    );

	    $('#champion_photo_upload').on(
	        'change', function(){
	            var reader = new FileReader();
	            reader.onload = function (event) {
	                $image_crop.croppie(
	                    'bind', 
	                    {
	                        url: event.target.result
	                    }
	                ).then(function(){
	                    console.log('jQuery bind complete');
	                });
	            }
	            reader.readAsDataURL(this.files[0]);
	            $('#uploadimageModal').modal('show');
	        }
	    );

	    $('.crop_image').click(function(event){
	        $image_crop.croppie(
	            'result', 
	            {
	                type: 'base64',
	                size: {width: 450},
	                format: 'jpeg',
	                quality: 0.8,
	                circle: false,
	            }
	        ).then(function(response){
	            $('#champion_photo').attr("src", response);
	            $.ajax(
	                {
	                    url:"/library/photo_uploader.php",
	                    type: "POST",
	                    data:
	                    {
	                        "id": <?php echo $_SESSION['id']; ?>,
	                        "event_id": <?php echo $event_id; ?>,
	                        "champ_photo": <?php echo '"'.$champ_photo_nostamp.'"'; ?>,
	                        "cropped_image": response
	                    },
	                    success:function(data)
	                    {
	                        $('#uploadimageModal').modal('hide');
	                        $('#champion_photo_upload').val('');
	                        $('#ajax_alert').html(data);
	                    }
	                }
	            );
	        })
	    });
	/* END AJAX Photo Uploader */ 
	</script>
{footer}
<?php ob_end_flush(); ?>
