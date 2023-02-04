<?php
// This file contains code used to populate the 'edit locations' table in the admin section of the staff page
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
$query = "SELECT LocationName, Description, KeyWords FROM Locations ORDER BY LocationName ASC LIMIT 100";
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
$tableContents .= '<thead><tr><th>Location Name</th><th>Description</th><th>Key Words</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {

  $columns = [
    // Location Name
    "<td>".$record[0]."</td>",
    // Decsription
    "<td>".formatColumn($record[1], '-')."</td>",
    // Key Words
    "<td>".formatColumn($record[2], '-')."</td>",
  ];

  // Appends this row to the table
  $tableContents .= formatRow($columns);
}

echo json_encode($tableContents);
?>
