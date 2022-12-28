<?php
// This file contains code used to populate an event table on the staff page
// All exported contents are json encoded and Ajax is used to regulary update the sections without refreshing the page

// Variables
// String used to contain the sections HTML code
$sectionHTML = '';
// Creates an array containing all of the background gradients
// Indexing this array gives the gradient that corresponds to the time of the index + 1 (e.g. for 12PM, array[13])
$backgrounds = [
  'linear-gradient(#012459 0%, #001322 100%)',
  'linear-gradient(#012459 0%, #001323 100%)',
  'linear-gradient(#003972 0%, #001322 100%)',
  'linear-gradient(#004372 0%, #00182b 100%)',
  'linear-gradient(#004372 0%, #011d34 100%)',
  'linear-gradient(#016792 0%, #00182b 100%)',
  'linear-gradient(#07729f 0%, #042c47 100%)',
  'linear-gradient(#12a1c0 0%, #07506e 100%)',
  'linear-gradient(#74d4cc 0%, #1386a6 100%)',
  'linear-gradient(#efeebc 0%, #61d0cf 100%)',
  'linear-gradient(#fee154 0%, #a3dec6 100%)',
  'linear-gradient(#fdc352 0%, #e8ed92 100%)',
  'linear-gradient(#ffac6f 0%, #ffe467 100%)',
  'linear-gradient(#fda65a 0%, #ffe467 100%)',
  'linear-gradient(#fd9e58 0%, #ffe467 100%)',
  'linear-gradient(#f18448 0%, #ffd364 100%)',
  'linear-gradient(#f06b7e 0%, #f9a856 100%)',
  'linear-gradient(#ca5a92 0%, #f4896b 100%)',
  'linear-gradient(#5b2c83 0%, #d1628b 100%)',
  'linear-gradient(#371a79 0%, #713684 100%)',
  'linear-gradient(#28166b 0%, #45217c 100%)',
  'linear-gradient(#192861 0%, #372074 100%)',
  'linear-gradient(#040b3c 0%, #233072 100%)',
  'linear-gradient(#040b3c 0%, #012459 100%)',
];

// Main
// Connects to the database
$con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
// SQL code to get all the event data (maximum of 25 events)
// Note, the event is formatted as follows: part1_part2
$sql = "SELECT EventID, LOWER(REPLACE(Event, ' ', '_')) AS TableID, StartTime, Event FROM Events";
// Saves the result of the SQL code to a variable
$result = $con->query($sql);
// Disconnects from the database
$con->close();

// For each event from the database, output a section containing its data
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  // Gets the starting hour of the event and stores it as an integer (e.g. 12 PM as 12, 4PM as 16 etc.)
  $hour = intval(substr($record[2], 0, 2));
  // Sets the table background to the background corresponding to the events starting time
  $tableBackground = $backgrounds[$hour];

  // The starting tag for this section
  $section_id = strtolower("$record[1]_event_section");
  $sectionHTML .= "<section class='main_section' id='$section_id'>";
  // The section title
  $title = ucwords(strtolower($record[3]));
  $sectionHTML .= "<h2>Events - $title</h2>";
  // The section description
  $description = "Here you can see all users who have signed out for the event, $record[3]. This includes the nature of the sign out, whether the user was early, late or on time";
  $sectionHTML .= "<p>$description</p><spacer></spacer>";
  // Declares the start of a table
  $table_id = strtolower("$record[1]_event_table");
  $sectionHTML .= "<div class='table_wrapper event_table_wrapper' style='background: $tableBackground'><header><h3>$title</h3></header><table class='table' id='$table_id'>";

  // Note that here should contain the tables contents
  // However, a separate PHP file populates the table once the event sections have been loaded

  // Declares the end of the table and section
  $sectionHTML .= "</table><footer/ class='event_footer'></div></section>";
}

// Outputs the sections HTML code for use by Ajax
echo json_encode($sectionHTML);
?>
