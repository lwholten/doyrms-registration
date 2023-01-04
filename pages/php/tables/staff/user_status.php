<?php
// This file contains code used to populate the 'user status' table on the staff page
// The table contents are json encoded and Ajax is used to regulary update the table without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'table_functions.php';

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
$tableContents .= '<thead><tr><th>Status</th><th>User</th><th>Current Location</th><th>Time last active</th><th>Date last active</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  // Array of indicator colours
  $indicators = [
    "<td><span class='inline-dot blue'></span> Away</td>",
    "<td><span class='inline-dot red'></span> Out</td>",
    "<td><span class='inline-dot green'></span> In</td>",
  ];

  $columns = [
    // Indicator
    // Indexes the array of indicator colours with the value given by the CASE clause in the SQL query
    $indicators[$record[0]],
    // User
    "<td".formatRestricted($record[6]).">".$record[1]." ".$record[2]."</td>",
    // Current Location
    "<td>".formatLocation($record[3])."</td>",
    // Time last active
    "<td>".$record[4]."</td>",
    // Date last active
    "<td>".$record[5]."</td>"
  ];

  // Appends this row to the table
  $tableContents .= formatRow($columns);
}

echo json_encode($tableContents);
?>
