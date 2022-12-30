<?php
// This file contains code used to create the event sections on the staff page
// All exported contents are json encoded and Ajax is used to regulary update the tables without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Variables
$tableContents = '';
$eventID = $_POST['eventID'];

// Main
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get the table data
// Note, this code MUST be changed in the future not to use a PHP variable in the query statement
$sql = "(SELECT Users.UserID, Users.Forename, Users.Surname, (CASE WHEN UserID IN (SELECT DISTINCT UserID FROM Log WHERE EventID=$eventID) THEN (SELECT Log.MinutesLate FROM Log WHERE Log.UserID=Users.UserID AND Log.EventID IS NOT NULL AND CAST(Log.LogTime AS DATE)=CURRENT_DATE ORDER BY LogID DESC LIMIT 1) ELSE NULL END) AS Timing, (CASE WHEN UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted FROM Users ORDER BY (CASE WHEN UserID IN (SELECT DISTINCT UserID FROM Log WHERE EventID=$eventID) THEN (SELECT Log.MinutesLate FROM Log WHERE Log.UserID=Users.UserID AND Log.EventID IS NOT NULL AND CAST(Log.LogTime AS DATE)=CURRENT_DATE ORDER BY LogID DESC LIMIT 1) ELSE NULL END), Users.Forename, Users.Surname ASC)";
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
$tableContents .= '<thead><tr><th>Nature</th><th>Forename</th><th>Surname</th><th>Timing</th></tr></thead>';
// Iterates through each record in the table, formats and appends the data to the variable 'tableContents'
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  $tableContents .= "<tr>";
  // Appends the nature of the event sign out (e.g. absent, late, early, on time)
  // Did not attend the event
  $tableContents .= "<td><span class='inline-dot ";
  if ($record[3] === NULL) {
    $tableContents .= "red'></span>Absent";
  }
  // Was late to the event
  elseif (intval($record[3]) < 0) {
    $tableContents .= "orange'></span>Late";
  }
  // Was on time to the event
  elseif (intval($record[3]) === 0) {
    $tableContents .= "green'></span>On Time";
  }
  // Was early to the event
  elseif (intval($record[3]) > 0) {
    $tableContents .= "blue'></span>Early";
  }
  // It is unknown and an error may have occured
  else {
    $tableContents .= "red'></span>Unknown";
  }
  $tableContents .= "</td>";
  // Appends the first and last names of the user
  if ($record[4] === 1 || $record[4] === "1") {
    // If the user is restricted, change the font
    $tableContents .= "<td class='restricted'> $record[1] </td>";
    $tableContents .= "<td class='restricted'> $record[2] </td>";
  }
  else {
    $tableContents .= "<td> $record[1] </td>";
    $tableContents .= "<td> $record[2] </td>";
  }
  // Appends the timing for the event (how many minutes late/early)
  $tableContents .= "<td>";
  if (intval($record[3]) > 0) {
    $mins = strval(abs(intval($record[3])));
    $tableContents .= "$mins Minutes Early";
  }
  elseif (intval($record[3]) < 0) {
    $mins = strval(abs(intval($record[3])));
    $tableContents .= "$mins minutes late";
  }
  elseif ($record[3] === NULL || intval($record[3]) === 0) {
    $tableContents .= "-";
  }
  else {
    $tableContents .= "N/A";
  }
  $tableContents .= "</td></tr>";
}

echo json_encode($tableContents);
?>
