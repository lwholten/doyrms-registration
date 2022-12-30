<?php
// This file contains code used to populate the 'recent activity' table on the staff page
// The table contents are json encoded and Ajax is used to regulary update the table without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Functions
function setRestrictedClass($tmp) {
  if ($tmp === 1 || $tmp === "1") {
    return "<td class='restricted'>";
  }
  else {
    return "<td>";
  }
}

// Variables
$tableContents = '';

// Main
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get the table data
/* Notes,
when record $record[0] = n
n = 0 -> Sign In (NOT Auto)
n = 1 -> Sign Out (NOT Auto)
n = 2 -> Sign In (Auto)
n = 3 -> Sign Out (Auto)
NULL -> User was automatically signed in (forgot to sign back in) */
$sql = "(SELECT (CASE WHEN Log.LocationID IS NULL AND Log.Auto=0 THEN 0 WHEN Log.LocationID IS NOT NULL AND Log.Auto=0 THEN 1 WHEN Log.LocationID IS NULL AND Log.Auto=1 THEN 2 WHEN Log.LocationID IS NOT NULL AND Log.Auto=1 THEN 3 ELSE NULL END) AS LogNature, Forename, Surname, LocationName, Event, CAST(LogTime AS time), CAST(LogTime AS date), (CASE WHEN Log.UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted FROM Log LEFT JOIN Locations ON Locations.LocationID = Log.LocationID LEFT JOIN Events ON Log.EventID = Events.EventID LEFT JOIN Users ON Users.UserID = Log.UserID ORDER BY LogTime DESC LIMIT 25)";
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
$tableContents .= '<thead><tr><th>In/Out</th><th>Time</th><th>Message</th><th>Event</th><th>Date</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  /*  Values for $record[n]:
  n = 0 -> the nature of the log (e.g log in/out/auto)
  n = 1 -> Forename
  n = 2 -> Surname
  n = 3 -> Location Name
  n = 4 -> Event ID
  n = 5 -> Log Time
  n = 6 -> Log Date */

  // If the user manually signed in
  if ($record[0] === 0 || $record[0] === "0") {
    $tableContents .= "<td><span class='inline-dot green'></span> In</td>";
    // We only need the time in format: HH:MM
    $time = substr($record[5], 0, 5);
    $tableContents .= "<td>$time</td>";
    // Sets the font color depending on whether the user is restricted or not
    $tableContents .= setRestrictedClass($record[7]);
    // The message displayed, the event, the date
    $tableContents .= "$record[1] $record[2] signed in</td><td>$record[4]</td><td>$record[6]</td></tr>";
  }
  // If the user manually signed out
  else if ($record[0] === 1 || $record[0] === "1") {
    $tableContents .= "<td><span class='inline-dot red'></span> Out</td>";
    // We only need the time in format: HH:MM
    $time = substr($record[5], 0, 5);
    $tableContents .= "<td>$time</td>";
    // Sets the font color depending on whether the user is restricted or not
    $tableContents .= setRestrictedClass($record[7]);
    // The message displayed, the event, the date
    $tableContents .= "$record[1] $record[2] signed out to $record[3]</td><td>$record[4]</td><td>$record[6]</td></tr>";
  }
  // If the user automatically signed in
  else if ($record[0] === 2 || $record[0] === "2") {
    $tableContents .= "<td><span class='inline-dot orange'></span> Alert</td>";
    // We only need the time in format: HH:MM
    $time = substr($record[5], 0, 5);
    $tableContents .= "<td>$time</td>";
    // Sets the font color depending on whether the user is restricted or not
    $tableContents .= setRestrictedClass($record[7]);
    // The message displayed, the event, the date
    $tableContents .= "$record[1] $record[2] forgot to sign back in!</td><td>$record[4]</td><td>$record[6]</td></tr>";
  }
  // If the user manually signed out
  else {
    $tableContents .= "<td><span class='inline-dot blue'></span> N/A</td>";
    // We only need the time in format: HH:MM
    $time = substr($record[5], 0, 5);
    $tableContents .= "<td>$time</td>";
    // Sets the font color depending on whether the user is restricted or not
    $tableContents .= setRestrictedClass($record[7]);
    // The message displayed, the event, the date
    $tableContents .= "$record[1] $record[2]</td><td>$record[4]</td><td>$record[6]</td></tr>";
  }
  $tableContents .= "</tr>";
}

echo json_encode($tableContents);
?>
