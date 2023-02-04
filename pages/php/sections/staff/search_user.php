<?php
// This file contains code used to display data for a searched user by a staff user
// The exported HTML code is json encoded and Ajax displays the data to the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Variables
$userID = $_POST['userID'];
$sectionHTML = '';

// Functions
// Used to determine whether the user is restricted and returns its respective code
function setRestrictedClass($tmp) {
  if ($tmp === 1 || $tmp === "1") {
    return "<td class='restricted'>";
  }
  else {
    return "<td>";
  }
}
// Used to show the details of a users restricted status
function fetchRestrictedDetails($userID, $restricted) {
  global $ini;
  // If the user is restricted
  if ($restricted === 1 || $restricted === '1') {
    $query = "SELECT CAST(DateTimeRestricted AS Date) AS Start, (CASE WHEN DateTimeUnrestricted IS NULL THEN 'Not Specified' ELSE CAST(DateTimeUnrestricted AS Date) END) AS End, (CASE WHEN Reason IS NULL THEN 'None' ELSE Reason END) AS Reason FROM RestrictedUsers WHERE UserID=?";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Creates a prepared statement
    $stmt = $con->prepare($query);
    // Binds the userID to the query
    $stmt->bind_param("i", $userID);
    //Executes the statement code
    $stmt->execute();
    //Binds the users data to an array
    $result = $stmt->get_result();
    $restrictedData = $result->fetch_array();
    //Disconnects from the database
    $con->close();
    /*Notes,
    $restrictedData[0] -> Start Date,
    $restrictedData[1] -> End Date ('Not specified' when NULL),
    $restrictedData[2] -> Description ('None' when NULL)
    */
    // Outputs the data as HTML in a user-friendly format
    return "<h5>Restricted: Yes</h5><divider></divider><h5>From: $restrictedData[0]</h5><h5>Until: $restrictedData[1]</h5><divider></divider><h5>Reason</h5><h6>$restrictedData[2]</h6>";
  }
  // If the user is not restricted
  else {
    return '<h5>Restricted: No</h5>';
  }
}
// Used to show the details of a users away status
function fetchAwayDetails($userID, $away) {
  global $ini;
  // If the user is restricted
  if ($away === 1 || $away === '1') {
    $query = "SELECT CAST(AwayUsers.DateTimeAway AS Date) AS Start, (CASE WHEN AwayUsers.DateTimeReturn IS NULL THEN 'Not Specified' ELSE CAST(AwayUsers.DateTimeReturn AS Date) END) AS End, (CASE WHEN AwayUsers.Reason IS NULL THEN 'None' ELSE AwayUsers.Reason END) AS Reason FROM AwayUsers WHERE UserID=?";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Creates a prepared statement
    $stmt = $con->prepare($query);
    // Binds the userID to the query
    $stmt->bind_param("i", $userID);
    //Executes the statement code
    $stmt->execute();
    //Binds the users data to an array
    $result = $stmt->get_result();
    $awayData = $result->fetch_array();
    //Disconnects from the database
    $con->close();
    /*Notes,
    $awayData[0] -> Start Date,
    $awayData[1] -> End Date ('Not specified' when NULL),
    $awayData[2] -> Description ('None' when NULL)
    */
    // Outputs the data as HTML in a user-friendly format
    return "<h5>Away: Yes</h5><divider></divider><h5>From: $awayData[0]</h5><h5>Until: $awayData[1]</h5><divider></divider><h5>Reason</h5><h6>$awayData[2]</h6>";
  }
  // If the user is not restricted
  else {
    return '<h5>Away: No</h5>';
  }
}
// Calculates the maximum height (in pixels) that the users activity table may be
function calcMaxTableHeight($restricted, $away) {
  $maxTableHeight = 260;
  // If the user is restricted, add 100px
  if ($restricted === 1 || $restricted === '1') {
    $maxTableHeight += 100;
  }
  // If the user is away, add 110px
  if ($away === 1 || $away === '1') {
    $maxTableHeight += 80;
  }
  // Return the max height
  return $maxTableHeight;
}

// Main

$query = "SELECT Users.Forename, Users.Surname, Users.Email, Users.Gender, Users.RoomNumber, (CASE WHEN Users.UserID IN (SELECT RestrictedUsers.UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted, (CASE WHEN Users.UserID IN (SELECT AwayUsers.UserID FROM AwayUsers) THEN 1 ELSE 0 END) AS Away, (CASE WHEN Users.LocationID IS NULL THEN NULL ELSE LocationName END) AS CurrentLocation FROM Users INNER JOIN Locations ON Users.LocationID = Locations.LocationID OR (Users.LocationID IS NULL) WHERE Users.UserID=? LIMIT 1";
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// Creates a prepared statement
$stmt = $con->prepare($query);
// Binds the userID to the query
$stmt->bind_param("i", $userID);
//Executes the statement code
$stmt->execute();
//Binds the users data to an array
$result = $stmt->get_result();
$userData = $result->fetch_array();
//Disconnects from the database
$con->close();

/* Notes,
userData[0] -> Forename
userData[1] -> Surname
userData[2] -> Email
userData[3] -> Gender
userData[4] -> Room Number
userData[5] -> Restricted (Boolean 1 or 0)
userData[6] -> Away (Boolean 1 or 0)
userData[7] -> Current Location*/

// Format the users data depending on the values recieved from the database
// If the users email is null, replace it with the text 'none'
if (is_null($userData[2])) {
  $userData[2] = 'None';
}
// Updates the users gender to a more user friendly format
if ($userData[3] === 'm') {
  $userData[3] = 'Male';
}
elseif ($userData[3] === 'f') {
  $userData[3] = 'Female';
}
elseif ($userData[3] === 'o') {
  $userData[3] = 'Other';
}
else {
  $userData[3] = 'Not Specified';
}
// If the users room is null, replace it with 'not specified'
if (is_null($userData[4])) {
  $userData[4] = 'Not Specified';
}
// If the users location is null, set it to 'the boarding house'
if (is_null($userData[7])) {
  $userData[7] = 'The Boarding House';
}

// Start of section and section header
$sectionHTML .= "<div id='user_details'><header><h3>User Details</h3></header><article><div class='details'>";
// The users forename and surname
$sectionHTML .= "<h3>$userData[0] $userData[1]</h3>";
// Current location
$sectionHTML .= "<span class='current_location'><h5>Current Location</h5><spacer></spacer><h5>$userData[7]</h5></span>";
// Start of user details
$sectionHTML .= "<divider></divider><h4>Details</h4><ul>";
// Email
$sectionHTML .= "<li><img src='../../images/svg/email.svg' alt='Email SVG'><spacer></spacer><h5>Email: $userData[2]</h5></li>";
// Gender
$sectionHTML .= "<li><img src='../../images/svg/genders.svg' alt='Genders SVG'><spacer></spacer><h5>Gender: $userData[3]</h5></li>";
// Room
$sectionHTML .= "<li><img src='../../images/svg/house.svg' alt='House SVG'><spacer></spacer><h5>Room: $userData[4]</h5></li>";
// Restricted
$sectionHTML .= "<li><img src='../../images/svg/padlock-closed.svg' alt='Locked padlock SVG'><spacer></spacer><div>".fetchRestrictedDetails($userID, $userData[5])."</div></li>";
// Away
$sectionHTML .= "<li><img src='../../images/svg/sign-out-door.svg' alt='Sign out door SVG'><spacer></spacer><div>".fetchAwayDetails($userID, $userData[6])."</div></li>";
// End of user details
$sectionHTML .= "</ul></div><spacer></spacer>";
// Start of users activity table
$sectionHTML .= "<div class='embedded_table_wrapper activity'><h4>Activity</h4><container class='scroll_container' style='max-height:".calcMaxTableHeight($userData[5], $userData[6])."px'><table id='user_details_activity_table'>";
// Note, Javascript is used to populate this table
// End of the users activity table and search user section
$sectionHTML .= "</table></container></div></article><footer/></div>";

// Outputs the HTML code for use by Ajax on the staff page
echo json_encode($sectionHTML);
?>
