<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

if (empty($_SESSION["id"])){
    header("Location: /login/");
    exit;
}

// Set the page title for the template
$page_title = "Your Profile Photo";

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
					$('#skip').text('Next').addClass('animate__animated animate__delay-4s animate__tada');
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

if (isset($_POST['skip-btn'])) {
	header('Location:/login/welcome/');
	exit;
}
    
?>
{header}
{main_nav}
<main role="main" id="onboarding_page">
    <h1 class="mb-5 sticky-top">Profile Photo</h1>
	<p class="text-center">
		Take a photo to complete your profile.<br>
		<small class="text-muted">This step is optional. You can add a photo later by editing your profile.</small>
	</p>

    <section class="form-row text-center">
		<div class="form-group col">
			<div class="form-row justify-content-center">
				<div id="photo_upload" class="custom-file col-lg-4 col-md-6 col-sm-7 ">
					<input type="file" id="photo_upload_button" class="d-inline custom-file-input" accept="image/*">
					<label class="custom-file-label" for="photo_upload_button">Take a selfie or choose from your library</label>
				</div>
			</div>
			<div id="ajax_alert"></div>
            <img class="rounded-circle" id="user_profile_photo" src="<?php echo $_SESSION['photo'];?>" alt="My Photo">
        </div>
	</section>
    <div class="text-center mt-4">
        <a href="/login/welcome/" id="skip" class="btn btn-primary mb-4">Skip</a>
    </div>

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
            <p>Drag the image to center your face in the circle. Zoom in to fill the circle. Save the image when you're satisfied.</p>
            <div id="croppie_element"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#photo_upload_button').val('');">Cancel</button>
            <button type="button" id="Save" class="btn btn-primary crop_image">Save</button>
          </div>
        </div>
      </div>
    </div>
    <!-- END: modal for photo cropping -->
</main> 
{footer}
<?php ob_end_flush(); ?>