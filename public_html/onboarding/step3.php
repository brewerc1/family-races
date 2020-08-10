<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

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

$('#profile_photo').on(
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
        $('#uploadimageModal').modal('show');
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
                    "cropped_image": response
                },
                success:function(data)
                {
                    $('#skip').text('Next');
					$('#uploadimageModal').modal('hide');
                    $('#profile_photo').val('');
					$('#ajax_alert').html(data);
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
    <p class="text-center">Take a photo to complete your profile.<br><small class="text-muted">This step is optional. You can add a photo later by editing your profile.</small></p>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
        <section class="form-row text-center">
            <div class="form-group col">
                <img class="rounded-circle" id="user_profile_photo" src="<?php echo $_SESSION['photo'];?>" alt="My Photo">
                <div id="ajax_alert"></div>
            </div>
        </section>
        <section class="form-row justify-content-center">    
            <div class="form-group col-md-4">
                <div id="photo_upload" class="form-group custom-file">
					<input type="file" id="profile_photo" class="custom-file-input" name="Add Photo" accept="image/*">
					<label class="custom-file-label" for="profile_photo">Take a selfie or choose a photo</label>
                </div>
            </div>
        </section>
        <div class="text-center">
            <a href="/login/welcome/" id="skip" class="btn btn-primary">Skip</a>
        </div>
    </form>

    <!-- modal for photo cropping -->
    <div class="modal" id="uploadimageModal" tabindex="-1" role="dialog" aria-labelledby="croppieModalLabel" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="croppieModalLabel">Adjust Your Photo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Drag the image to center your face in the circle. Zoom in to fill the circle with your face. Save the image when you're satisfied.</p>
            <div id="croppie_element"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" id="Save" class="btn btn-primary crop_image">Save</button>
          </div>
        </div>
      </div>
    </div>
    <!-- END: modal for photo cropping -->
</main> 
{footer}
<?php ob_end_flush(); ?>