<?php
// This file contains code that displays dashboard analytics on the staff page
// The exported HTML code is json encoded and Ajax is used to regulary update the dashboard without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Functions
function analytics_users() {
  global $ini;
  // Variables
  $usersHTML = '';

  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Code used to get all users, signed in, signed out, away and restricted
  $query = '(SELECT
  COUNT(*) AS Total,
  (COUNT(*) - COUNT(LocationID)) AS SignedIn,
  (SELECT COUNT(UserID) FROM Users WHERE UserID IN (SELECT UserID FROM RestrictedUsers) AND UserID IN (SELECT UserID FROM Users WHERE LocationID IS NULL)) AS RestrictedSignedIn,
  COUNT(LocationID) AS SignedOut,
  (SELECT COUNT(UserID) FROM Users WHERE UserID IN (SELECT UserID FROM RestrictedUsers) AND UserID IN (SELECT UserID FROM Users WHERE LocationID IS NOT NULL)) AS RestrictedSignedOut,
  (SELECT COUNT(*) FROM AwayUsers) AS Away,
  (SELECT COUNT(UserID) FROM Users WHERE UserID IN (SELECT UserID FROM RestrictedUsers) AND UserID IN (SELECT UserID FROM AwayUsers)) AS RestrictedAway
  FROM Users)';
  // Saves the result of the SQL code to a variable
  $result = $con->query($query);
  // Disconnects from the database
  $con->close();

  // Iterates through the table records and displays them on the web page's table
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Notes:
    // $record[0] -> Total users
    // $record[1] -> Signed in
    // $record[2] -> Signed in & Restricted
    // $record[3] -> Signed out
    // $record[4] -> Signed out & Restricted
    // $record[5] -> Away
    // $record[6] -> Away & Restricted

    // Calculates the number of users that correspond to each parameter
    $userData = array (
        // Total users
        "total"  => $record[0],
        // Away = (away - away & restricted)
        "away"  => ($record[5] - $record[6]),
        // Away & restricted
        "awayr"  => $record[6],
        // In = (in - in & restricted)
        "in"  => ($record[1] - $record[2]),
        // In & restricted
        "inr"  => $record[2],
        // Out = (out - out & restricted - away - away & restricted)
        // This simplifies to: (out - out & restricted + away & restricted - away)
        // I honestly don't know why, but this combination works, so lets role with it anyway ¯\_(ツ)_/¯
        "out"  => ($record[3] - $record[4] + $record[6] - $record[5]),
        // Out & restricted = (out & restricted - away & restricted)
        "outr"  => ($record[4] - $record[6])
    );

    // Calculates the percentage of the user bar that each parameter will occupy
    // Note that these values are rounded UP, if the total width exceeds 100%, it will be corrected automatically by the CSS
    $barData = [
      // Signed in
      // Signed in is the number of signed in users excluding those that are restricted
      [round(($userData["in"] / $userData["total"]) * 100), 'green'],
      // Signed in & restricted
      [round(($userData["inr"] / $userData["total"]) * 100), 'orange-green-stripes'],
      // Signed out
      // Signed out is the number of signed out users excluding those that are away
      [round(($userData["out"] / $userData["total"]) * 100), 'red'],
      // Signed out & restricted
      [round(($userData["outr"] / $userData["total"]) * 100), 'orange-red-stripes'],
      // Away
      // This is the number of away users excluding both restricted and away
      [round(($userData["away"] / $userData["total"]) * 100), 'blue'],
      // Restricted and away
      // If users are both restricted and away, they area treated as being just away but the restricted attribute is still applied
      [round(($userData["awayr"] / $userData["total"]) * 100), 'orange-blue-stripes'],
    ];
    // Error correction for the bar widths, in case a value happens to be negative or over one hundred
    for ($i = 0; $i <= (count($barData)-1); $i++) {
      if ($barData[$i][0] < 0) {
        $barData[$i][0] = 0;
      }
      elseif ($barData[$i][0] > 100) {
        $barData[$i][0] = 100;
      }
    }
    // The values used for the bar legend
    // Priority of information: signed in -> signed out -> restricted -> away -> restricted and away
    $legendValues = [
      // Signed in is given by the number of signed in users excluding those that are restricted
      ['green', 'Signed In', ($userData["in"] + $userData["inr"])],
      // Signed out is given by the number of signed in users excluding those that are away
      ['red', 'Signed Out', ($userData["out"] + $userData["outr"])],
      // Away in is given by the number of away users excluding those that are both restricted and away
      ['blue', 'Away', ($userData["away"] + $userData["awayr"])],
      // Restricted in is given by the number of restricted users excluding those that are both restricted and away
      ['orange', 'Restricted', ($userData["inr"] + $userData["outr"] + $userData["awayr"])],
    ];
    // Error correction for the legend values, in case a value happens to be negative it is set to zero
    for ($i = 0; $i <= (count($legendValues)-1); $i++) {
      if ($legendValues[$i][2] < 0) {
        $legendValues[$i][2] = 0;
      }
    }

    // Starts the 'users' part
    $usersHTML .= "<container class='dashboard_wrapper'><div class='user_analytics'><h5>Users</h5>";
    $usersHTML .= "<h6>Total Users: $record[0]</h6>";
    // Outputs the user bar
    $usersHTML .= "<div class='user_bar'>";
    foreach($barData as $data){
      if ($data[0] != 0) {
        $usersHTML .= "<span style='width: $data[0]%' class='$data[1]'></span>";
      }
    }
    // Outputs the legend for the user bar
    $usersHTML .= "</div><ul class='user_bar_legend'>";
    foreach($legendValues as $value){
      if ($value[2] != 0) {
        $usersHTML .= "<li><h6><span class='inline-bar $value[0]'></span>$value[2] $value[1]</h6></li>";
      }
    }
    // Ends the 'users' part
    $usersHTML .= "</ul></div></container>";
    // Does this only once (for the first set of values that are returned)
    break;
  }
  return $usersHTML;
}
function analytics_activity() {
  global $ini;
  // Variables
  $activityHTML = '';

  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Code used to get all users, signed in, signed out, away and restricted
  $query = '(SELECT (SELECT COUNT(*) FROM Log WHERE cast(LogTime AS Date)=CURRENT_DATE AND LocationID IS NOT NULL AND Auto=0) AS SignOuts, (SELECT COUNT(*) FROM Log WHERE cast(LogTime AS Date)=CURRENT_DATE AND LocationID IS NULL AND Auto=0) AS SignIns, (SELECT COUNT(*) FROM Log WHERE cast(LogTime AS Date)=CURRENT_DATE AND LocationID IS NULL AND Auto=1) AS AutoSignIns, (SELECT COUNT(*) FROM Log WHERE cast(LogTime AS Date)=CURRENT_DATE AND MinutesLate > 0) AS Earlys, (SELECT COUNT(*) FROM Log WHERE cast(LogTime AS Date)=CURRENT_DATE AND MinutesLate < 0) AS Late)';

  // Saves the result of the SQL code to a variable
  $result = $con->query($query);
  // Disconnects from the database
  $con->close();

  // Iterates through the table records and displays them on the web page's table
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Notes:
    // $record[0] -> Sign outs
    // $record[1] -> Sign ins
    // $record[2] -> Auto sign ins
    // $record[3] -> Earlys
    // $record[4] -> Lates

    $metricValues = [
      [$record[0], 'Users have signed out'],
      [$record[1], 'Users have signed in'],
      [$record[2], 'Users forgot to sign in'],
      [$record[3], 'Users have been early'],
      [$record[4], 'Users have been late'],
    ];

    // Starts the 'activity' part
    $activityHTML .= "<container class='dashboard_wrapper'><div class='activity_analytics'><h5>Todays Activity</h5><spacer></spacer><ul>";
    // Outputs each metric
    foreach($metricValues as $value){
      $activityHTML .= "<li><h5>$value[0]</h5><h6>$value[1]</h6></li>";
    }
    // Ends the 'activity' part
    $activityHTML .= "</ul></div></container>";

    // Does this only once (for the first set of values that are returned)
    break;
  }
  return $activityHTML;
}

// Variables
$analyticsHTML = '';

// Main
$analyticsHTML .= analytics_users();
$analyticsHTML .= analytics_activity();
// Outputs the analytics HTML code for use by Ajax
echo json_encode($analyticsHTML);

?>
