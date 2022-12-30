<?php
// This file contains code used to populate the 'user status' table on the staff page
// The table contents are json encoded and Ajax is used to regulary update the table without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Variables
$tableContents = '';

// Main
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get the table data
$sql = "(SELECT (CASE WHEN UserID IN (SELECT UserID FROM AwayUsers) THEN 0 WHEN Users.LocationID IS NULL THEN 2 ELSE 1 END) AS Type, Forename, Surname, LocationName, cast(LastActive AS time), cast(LastActive AS date), (CASE WHEN UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted FROM Users LEFT JOIN Locations ON Locations.LocationID = Users.LocationID ORDER BY Type, Users.Forename ASC LIMIT 100)";
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

// The table header
$tableContents .= '<thead><tr><th>Status</th><th>Forename</th><th>Surname</th><th>Current Location</th><th>Time last active</th><th>Date last active</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  // Iterates through each item of the record
  for ($i = 0; $i <= (count($record)-1); $i++) {
    if ($i === 0) {
      // 0 -> Away, 1 -> Out, 2 ->
      $tableContents .= "<tr><td>";
      if ($record[$i] === 2 || $record[$i] === "0") {
        $tableContents .= "<span class='inline-dot blue'></span> Away";
      }
      else if ($record[$i] === 0 || $record[$i] === "1") {
        $tableContents .= "<span class='inline-dot red'></span> Out";
      }
      else if ($record[$i] === 1 || $record[$i] === "2") {
        $tableContents .= "<span class='inline-dot green'></span> In";
      }
      else {
        $tableContents .= "<span class='inline-dot orange'></span> N/A";
      }
      $tableContents .= "</td>";
    }
    else if ($i === 1 || $i === 2) {
      // If the user is restricted, change the font color to red
      if ($record[6] === 1 || $record[6] === "1") {
        $tableContents .= "<td class='restricted'> $record[$i] </td>";
      }
      else {
        $tableContents .= "<td> $record[$i] </td>";
      }
    }
    // 3 is the index of the 'location' column, if it is set to NULL, the user is signed in
    else if ($i === 3) {
      if ($record[$i] === "0" || $record[$i] === 0 || $record[$i] === NULL) {
        $tableContents .= "<td>The Boarding House</td>";
      }
      else {
        $tableContents .= "<td>$record[$i]</td>";
      }
    }
    else if ($i === 4) {
      // We only need the time in format: HH:MM
      $time = substr($record[$i], 0, 5);
      $tableContents .= "<td>$time</td>";
    }
    // Skips the final column
    else if ($i === 6) {
      continue;
    }
    // If it is any other column
    else {
      $tableContents .= "<td> $record[$i] </td>";
    }
  };
}

echo json_encode($tableContents);
?>
