<?php
// This file contains code used to fetch the ID's of all event tables
// All exported contents are json encoded and Ajax uses these ID's to update the event tables

// Variables
// An array used to contain the table and event IDs
$data = [];

// Main
// Connects to the database
$con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
// SQL code to select the table event names (maximum of 25 events)
// Note, the event name is formatted as follows: part1_part2, so it may be used as the tables ID in the HTML code
$sql = "SELECT LOWER(REPLACE(Event, ' ', '_')) AS TableID, EventID FROM Events";
// Saves the result of the SQL code to a variable
$result = $con->query($sql);
// Disconnects from the database
$con->close();

// For each event from the database, append an ID for the events table to an array
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  // Appends an array containing the tableID and eventID
  array_push($data, [$record[0].'_event_table', $record[1]]);
}

// Outputs the sections HTML code for use by Ajax
echo json_encode($data);
?>
