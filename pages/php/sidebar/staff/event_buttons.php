<?php
// This file contains code that displays the event buttons on the staff page
// Note that it is not json encoded since they are loaded only once, when the page is loaded
// Ajax is not required to refresh the event buttons at regular intervals

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get all the event data (maximum of 25 events)
$sql = "SELECT LOWER(REPLACE(Event, ' ', '_')) AS Event, Event FROM Events";
// Saves the result of the SQL code to a variable
$result = $con->query($sql);
// Disconnects from the database
$con->close();

// For each event from the database, echo a button which leads to its section
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  // generates an id for the section
  $section_id = strtolower("$record[0]_event_section");
  // generates an id for the button
  $button_id = strtolower("$record[0]_event_button");
  // generates the text displayed for the button
  $name = ucwords(strtolower($record[1]));
  // echos the generated button
  echo "<li onclick='toggleMainSection(this.id,`$section_id`)' class='sidebar_button' id='$button_id'><h4>$name</h4></li>";
}
?>
