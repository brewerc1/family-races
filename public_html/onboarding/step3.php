<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

if (isset($_POST['skip-btn'])) {
    Header('Location:/races/index.php');
}
if (isset($_POST['submit-btn'])) {
    //User Photo Upload
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

if (!isset($_SESSION["id"])){
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

$uploadsql = "UPDATE user SET photo = :photo_value WHERE id ={$_SESSION['id']}";
$updatePhoto = $pdo->prepare($uploadsql);
$updatePhoto->execute(['photo_value' => $photo_value]);
if ($updatePhoto)  {
    $getsessionsql ="SELECT * FROM user where id = {$_SESSION['id']}";
    $updatesession = $pdo->prepare($getsessionsql);
    $updatesession->execute();

    }
    }
    
?>
{header}
{main_nav}
<main>
<h1>Your Photo</h1>
        <div>
            <img src="no-user-image.jpg">
        </div>
    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
        <p>If you do not submit a image you will be given a no profile photo image as your photo</p>
        <div class="form-group" id="photo_upload">
            <input type="file" class="form-control-file" id="user_profile_photo" name="profile_photo" placeholder="Add a Profile Photo">
        </div>
        <input type ="submit" class="btn btn-primary" id="profile_photo" name="sumbit-btn" value="Upload">
        <div>
            <input type ="submit" class="btn btn-primary" name="skip-btn" value="Skip">
        </div>
        <input class="col" type="hidden" id="cropped_image">
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
                <button type="button" class="btn btn-primary crop_image">Save</button>
              </div>
            </div>
          </div>
        </div>
        <!-- END: modal for photo cropping -->
    </main>
{footer}
<?php ob_end_flush(); ?>