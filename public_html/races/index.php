<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Get UID
$uid = $_GET['u'];

// Get event
$event = $_GET['e'];

// Get race
$race = $_GET['r'];

// Gather data for this page
// SQL to retrieve race results
$race_sql = 'SELECT user.first_name, user.last_name, user.photo, race_standings.race_event_id, race_standings.race_race_number, race_standings.user_id, race_standings.earnings, event.name, event.date 
FROM race_standings 
INNER JOIN event ON event.id = race_standings.race_event_id 
INNER JOIN user ON user.id = race_standings.user_id 
WHERE race_standings.race_event_id = :event AND race_standings.race_race_number = :race 
ORDER BY race_standings.race_event_id, race_standings.race_race_number, race_standings.earnings DESC';
$race_result = $pdo->prepare($race_sql);
$race_result->execute(['event' => $event, 'race' => $race]);
$num_race_results = $race_result->rowCount();

// SQL to determine this user's pick for this race
$pick_sql = "SELECT * FROM `pick` WHERE pick.user_id = :user_id AND pick.race_event_id = :event AND pick.race_race_number = :race LIMIT 1";
$pick_result = $pdo->prepare($pick_sql);
$pick_result->execute(['user_id' => $uid, 'event' => $event, 'race' => $race]);
$pick = $pick_result->fetch();

// SQL to calculate number of races in this event
$num_races_sql = "SELECT * FROM `race` WHERE race.event_id = :event";
$num_races_result = $pdo->prepare($num_races_sql);
$num_races_result->execute(['event' => $event]);
$num_races = $num_races_result->rowCount();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <title>Races</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        /* DEVELOPMENT CSS */
        html,body {
            margin: 0;
            padding: 0;
        }
        div {
            border: 1px solid rgba(0,0,0,.1);
        }
        div div {
            margin: 2px;
        }
        pre {
          border: 1px solid #ccc;
          background-color: rgba(0,0,0,.05);
          color: rgba(0,0,0,.65);
          padding-left: 5px;
          position: fixed;
          top: 0;
          left: 0;
        }
    </style>
    <script>
      $(document).ready(function(){
        $(function(){
          // bind change event to select
          $('#race_picker').on('change', function () {
              var url = "/races/?" + $(this).val(); // get selected value
              if (url) { // require a URL
                  window.location = url; // redirect
              }
              return false;
          });
        });
      });
  </script>
</head>
<body>

<div id="page-wrapper">
    <form method="post" action="./invite.php" id="race">
        <select id="race_picker">
<?php 
for($i = 1; $i <= $num_races; $i++){
  if($i == $race){
    $attr = "selected='selected' disabled='disabled'";
  }else{
    $attr = "";
  }
  echo "<option value='e=$event&r=$i&u=$uid' $attr>Race $i</option>";
}
?>
          <option value="e=$event&r=all&u=$uid">All Races</option>
        </select>
    </form>
    <p><strong>You Bet:</strong> <?php echo "{$pick['horse_number']} to {$pick['finish']}";?><br>
    <strong>Purse:</strong> $<?php //echo $purse;?></p>

<?php

if ($num_race_results > 0) {
  $invited = "";

  // Output data of each row
  while($row = $race_result->fetch()) {
    $name = $row["first_name"] . ' ' . $row["last_name"];
      // Handle missing profile photo
      if(empty($row["photo"])) {
          $photo = "https://races.informatics.plus/images/no-user-image.jpg";
      }else{
          $photo = $row["photo"];
      }
      echo "<div class='user-row'><a href='/user/user_profile?uid=" . $row["user_id"] . "'><img src='$photo' alt='photo'></a><span>$name</span> <span class='earnings'>\${$row["earnings"]}</span></div>";
    }
  } else {
    echo "0 results";
  }



//echo "<pre><b>UID:</b> $uid<br><b>Event:</b> $event<br><b>Race:</b> $race</pre>";
?>
</div> <!-- end id page-wrapper -->


<!-- In support of Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</body>
</html>