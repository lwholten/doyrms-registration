<?php
// This file contains code used to populate the 'recent activity' table on the staff page
// The table contents are json encoded and Ajax is used to regulary update the table without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../table_functions.php';

// Variables
$tableContents = '';

// Main
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get the table data
/* Notes,
when record $record[0] = n
n = 0 -> user signed in
n = 1 -> user signed out
n = 2 -> user was signed in by the system (automatically)
n = 3 -> user was signed out by the system (automatically)
n = 4 -> user was signed in by a staff user (not for event)
n = 5 -> user was signed out by a staff user (not for event)
n = 6 -> user was signed in by a staff user (for event)
n = 7 -> user was signed out by a staff user (for event)
NULL -> User was automatically signed in (forgot to sign back in) */
$sql = "(SELECT
(CASE WHEN Log.LocationID IS NULL AND Log.Auto=0 AND Log.StaffAction=0 THEN 0
  WHEN Log.LocationID IS NOT NULL AND Log.Auto=0 AND Log.StaffAction=0 THEN 1
  WHEN Log.LocationID IS NULL AND Log.Auto=1 AND Log.StaffAction=0 THEN 2
  WHEN Log.LocationID IS NOT NULL AND Log.Auto=1 AND Log.StaffAction=0 THEN 3
  WHEN Log.LocationID IS NULL AND Log.StaffAction=1 AND Log.EventID IS NULL THEN 4
  WHEN Log.LocationID IS NOT NULL AND Log.StaffAction=1 AND Log.EventID IS NULL THEN 5
  WHEN Log.LocationID IS NULL AND Log.StaffAction=1 AND Log.EventID IS NOT NULL THEN 6
  WHEN Log.LocationID IS NOT NULL AND Log.StaffAction=1 AND Log.EventID IS NOT NULL THEN 7
ELSE NULL END) AS LogNature,
Forename, Surname, LocationName, Event,
CAST(LogTime AS time),
CAST(LogTime AS date),
(CASE WHEN Log.UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted,
StaffMessage,
Log.UserID
FROM Log
LEFT JOIN Locations ON Locations.LocationID = Log.LocationID
LEFT JOIN Events ON Log.EventID = Events.EventID
LEFT JOIN Users ON Users.UserID = Log.UserID
ORDER BY LogTime DESC LIMIT 25)";
// Saves the result of the SQL code to a variable
$result = $con->query($sql);
// Disconnects from the database
$con->close();

// If the table is empty, echo the placeholder for an empty table
if (!(mysqli_num_rows($result) > 0)) {
  $tableContents = "<div class='empty_table_placeholder'><h3>Hmm...</h3><text>¯\_(ツ)_/¯</text><h3>...There seems to be no activity</h3></div>";
  echo json_encode($tableContents);
  exit();
}

// The table header
$tableContents .= '<thead><tr><th>Nature</th><th>Time</th><th>User</th><th>Message</th><th>Location</th><th>Event</th><th>Date</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  /*  Values for $record[n]:
  n = 0 -> the nature of the log (e.g log in/out/auto)
  n = 1 -> Forename
  n = 2 -> Surname
  n = 3 -> Location Name
  n = 4 -> Event ID
  n = 5 -> Log Time
  n = 6 -> Log Date
  n = 7 -> Restricted
  n = 8 -> Staff Message
  n = 9 -> UserID*/

  // User signed in
  if ($record[0] == 0) {
    $columns = [
      // In/Out column
      "<td><span class='inline-dot green'></span> In</td>",
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>-</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
  // User signed out
  else if ($record[0] == 1) {
    $columns = [
      // In/Out column
      "<td><span class='inline-dot red'></span> Out</td>",
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>-</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
  // User was signed in automatically
  else if ($record[0] == 2) {
    $columns = [
      // In/Out column
      "<td><span class='inline-dot green'></span><span class='inline-dot orange'></span> In</td>",
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>This user was signed in automatically</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
  // User was signed out automatically
  else if ($record[0] == 3) {
    $columns = [
      // In/Out column
      "<td><span class='inline-dot red-orange'></span><span class='inline-dot orange'></span> Out</td>",
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>This was signed out automatically</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
  // User was signed in by a staff user (not for an event)
  else if ($record[0] == 4) {

    // If there is a message, display that message instead of the default
    if (is_null($record[8]) || empty($record[8])) {
      $msg = $record[1].' was signed in by a member of staff';
    }
    else {
      $msg = $record[8];
    }

    $columns = [
      // In/Out column
      "<td><span class='inline-dot green'></span><span class='inline-dot blue'></span> In</td>",
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>".$msg."</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
  // User was signed out by a staff user (not for an event)
  else if ($record[0] == 5) {

    // If there is a message, display that message instead of the default
    if (is_null($record[8]) || empty($record[8])) {
      $msg = $record[1].' was signed out by a member of staff';
    }
    else {
      $msg = $record[8];
    }

    $columns = [
      // In/Out column
      "<td><span class='inline-dot red'></span><span class='inline-dot blue'></span> Out</td>",
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>".$msg."</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
  // User was signed in by a staff user (for an event)
  else if ($record[0] == 6) {
    
    // If there is not a staff message
    if (is_null($record[8]) || empty($record[8])) {
      // If the location is null or empty
      if (is_null($record[3]) || empty($record[3])) {
        $msg = $record[1].' was marked as attending an event by a staff user';
        $indicator = "<td><span class='inline-dot blue'></span> Event</td>";
      }
      // If the location was set
      else {
        $msg = $record[1].' was signed in for an event by a staff user';
        $indicator = "<td><span class='inline-dot green'></span><span class='inline-dot blue'></span> In</td>";
      }
    }
    else {
      // Set the message to the staff message
      $msg = $record[8];

      // If the location is null or empty
      if (is_null($record[3]) || empty($record[3])) { $indicator = "<td><span class='inline-dot blue'></span> Event</td>"; }
      // If the location was set
      else { $indicator = "<td><span class='inline-dot green'></span><span class='inline-dot blue'></span> In</td>"; }      
    }

    $columns = [
      // In/Out column
      $indicator,
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>".$msg."</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
  // User was signed out by a staff user (for an event)
  else if ($record[0] == 7) {

    // If there is not a staff message
    if (is_null($record[8]) || empty($record[8])) {
      // If the location is null or empty
      if (is_null($record[3]) || empty($record[3])) {
        $msg = $record[1].' was marked as attending an event by a staff user';
        $indicator = "<td><span class='inline-dot blue'></span> Event</td>";
      }
      // If the location was set
      else {
        $msg = $record[1].' was signed out for an event by a staff user';
        $indicator = "<td><span class='inline-dot red'></span><span class='inline-dot blue'></span> Out</td>";
      }
    }
    else {
      // Set the message to the staff message
      $msg = $record[8];

      // If the location is null or empty
      if (is_null($record[3]) || empty($record[3])) { $indicator = "<td><span class='inline-dot blue'></span> Event</td>"; }
      // If the location was set
      else { $indicator = "<td><span class='inline-dot red'></span><span class='inline-dot blue'></span> Out</td>"; }      
    }

    $columns = [
      // In/Out column,
      $indicator,
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>".$msg."</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
  // If the user manually signed out
  // Out (Auto) (Staff Action)
  else {
    $columns = [
      // In/Out column
      "<td><span class='inline-dot gre'></span> N/A</td>",
      // Time
      "<td>".substr($record[5], 0, 5)."</td>",
      // User
      "<td".formatRestricted($record[7]).">".$record[1]." ".$record[2]."</td>",
      // Message
      "<td>".$record[1]." did something, but we're not too sure what it was ¯\_(ツ)_/¯</td>",
      // Location
      "<td>".formatColumn($record[3])."</td>",
      // Event
      "<td>".formatColumn($record[4])."</td>",
      // Date
      "<td>".$record[6]."</td>"
    ];

    $tableContents .= formatRow($columns, $record[9]);
  }
}

echo json_encode($tableContents);
?>
