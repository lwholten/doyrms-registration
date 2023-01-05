<?php
// This file contains code used to create the event sections on the staff page
// All exported contents are json encoded and Ajax is used to regulary update the tables without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'table_functions.php';

// Functions
// Determines what indicator best suits a users timing
function fetchIndicatorIndex($time) {
  if ($time > 0) {
    return 'late';
  }
  elseif ($time === 0 || $time === "0") {
    return 'on time';
  }
  elseif ($time < 0) {
    return 'early';
  }
  else {
    return 'absent';
  }
}
// Determines the appropriate suffix for a users timing (early, late etc)
function formatTiming($time) {
  $mins = strval(abs(intval($time)));
  if ($time > 0) {
    return $mins." Minutes Late";
  }
  elseif ($time < 0) {
    return $mins." Minutes Early";
  }
  else {
    return "-";
  }
}

// Variables
$tableContents = '';
$eventID = $_POST['eventID'];

// Main
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get the table data
// Note, this code MUST be changed in the future not to use a PHP variable in the query statement
$sql = "SELECT Users.UserID, Users.Forename, Users.Surname, (SELECT Log.MinutesLate FROM Log WHERE Log.UserID=Users.UserID AND CAST(Log.LogTime AS DATE)=CURRENT_DATE AND Log.EventID=$eventID) AS Timing, (SELECT Log.StaffMessage FROM Log WHERE Log.UserID=Users.UserID AND CAST(Log.LogTime AS DATE)=CURRENT_DATE AND Log.EventID=$eventID) AS Message, (CASE WHEN UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted FROM Users ORDER BY Users.Forename, Users.Surname";
// Saves the result of the SQL code to a variable
$result = $con->query($sql);
// Disconnects from the database
$con->close();

// If the table is empty, echo the placeholder for an empty table
if (!(mysqli_num_rows($result) > 0)) {
  $tableContents = "<div class='empty_table_placeholder'><h3>Hmm...</h3><text>¯\_(ツ)_/¯</text><h3>...There seem to be no users on the system</h3></div>";
  echo json_encode($tableContents);
  exit();
}

// Appends the table header
$tableContents .= '<thead><tr><th>Nature</th><th>User</th><th>Timing</th><th>Message</th></tr></thead>';
// Iterates through each record in the table, formats and appends the data to the variable 'tableContents'
while($record = $result -> fetch_array(MYSQLI_NUM)) {

  // Array of indicators
  $indicators = [
    'late' => "<td><span class='inline-dot orange'></span>Late</td>",
    'on time' => "<td><span class='inline-dot green'></span>On Time</td>",
    'early' => "<td><span class='inline-dot blue'></span>Early</td>",
    'absent' => "<td><span class='inline-dot red'></span>Absent</td>",
  ];

  $columns = [
    // Nature (indicators)
    $indicators[fetchIndicatorIndex($record[3])],
    // User
    "<td".formatRestricted($record[5]).">".$record[1]." ".$record[2]."</td>",
    // Timing
    "<td>".formatTiming($record[3])."</td>",
    // Message
    "<td>".formatColumn($record[4], 'None')."</td>"
  ];

  $tableContents .= formatRow($columns);
}

echo json_encode($tableContents);
?>
