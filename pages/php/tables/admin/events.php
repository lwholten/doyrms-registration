<?php
// This file contains code used to populate the 'edit events' table in the admin section of the staff page
// The table contents are json encoded and Ajax is used to regulary update the table without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../table_functions.php';

// Variables
$tableContents = '';

// Functions
// Outputs a coma-separated list of days using a string of format 'MTWT...' as the input
function formatDays($daysStr) {
  $daysList = "";

  if (str_contains($daysStr, "M")) {$daysList .= "Mon, ";}
  if (str_contains($daysStr, "T")) {$daysList .= "Tue, ";}
  if (str_contains($daysStr, "W")) {$daysList .= "Wed, ";}
  if (str_contains($daysStr, "R")) {$daysList .= "Thu, ";}
  if (str_contains($daysStr, "F")) {$daysList .= "Fri, ";}
  if (str_contains($daysStr, "U")) {$daysList .= "Sat, ";}
  if (str_contains($daysStr, "S")) {$daysList .= "Sun, ";}

  return $daysList;
}
// Outputs a string based on an events timing
function formatTiming($timing) {
  if ($timing == 0) {
    return "On Time";
  }
  else {
    return $timing." Minutes early/late";
  }
}

// Main
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get the table data
$query = "SELECT Event, LocationName, (CASE WHEN SignInEvent=1 THEN 'Sign In' WHEN SignInEvent=0 THEN 'Sign Out' END) AS Nature, Days, Deviation, StartTime, EndTime, (CASE WHEN Alerts=1 THEN 'Yes' WHEN Alerts=0 THEN 'No' END) AS Alerts FROM Events LEFT Join Locations On Events.LocationID=Locations.LocationID ORDER BY StartTime ASC LIMIT 10";
// Saves the result of the SQL code to a variable
$result = $con->query($query);
// Disconnects from the database
$con->close();

// If the table is empty, echo the placeholder for an empty table
if (!(mysqli_num_rows($result) > 0)) {
  $tableContents = "<div class='empty_table_placeholder'><h3>Hmm...</h3><text>¯\_(ツ)_/¯</text><h3>...It seems like there are no locations.</h3></div>";
  echo json_encode($tableContents);
  exit();
}

// The tables header
$tableContents .= '<thead><tr><th>Event</th><th>Where</th><th>Action</th><th>Days</th><th>Timing</th><th>Start</th><th>End</th><th>Alerts</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {

  $columns = [
    // Event
    "<td>".ucwords($record[0])."</td>",
    // Location
    "<td>".ucwords($record[1])."</td>",
    // Nature
    "<td>".$record[2]."</td>",
    // Days
    "<td>".formatDays($record[3])."</td>",
    // Timing
    "<td>".formatTiming($record[4])."</td>",
    // Start Time
    "<td>".formatColumn(substr($record[5], 0, 5), '-')."</td>",
    // End Time
    "<td>".formatColumn(substr($record[6], 0, 5), '-')."</td>",
    // Alerts
    "<td>".$record[7]."</td>"
  ];

  // Appends this row to the table
  $tableContents .= formatRow($columns);
}

echo json_encode($tableContents);
?>
