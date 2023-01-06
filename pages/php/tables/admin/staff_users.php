<?php
// This file contains code used to populate the 'edit staff' table in the admin section of the staff page
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
$query = "SELECT (CASE WHEN AccessLevel=1 THEN 'Staff' WHEN AccessLevel=2 THEN 'Moderator' WHEN AccessLevel=3 THEN 'Admin' ELSE NULL END) AS Access, Username, Forename, Surname, Email, CAST(LastChangedPassword AS Date) AS LastChangedPassword FROM Staff ORDER BY AccessLevel, Forename, Surname DESC LIMIT 50";
// Saves the result of the SQL code to a variable
$result = $con->query($query);
// Disconnects from the database
$con->close();

// If the table is empty, echo the placeholder for an empty table
if (!(mysqli_num_rows($result) > 0)) {
  $tableContents = "<div class='empty_table_placeholder'><h3>Hmm...</h3><text>¯\_(ツ)_/¯</text><h3>...There are no staff users! How are you seeing this?</h3></div>";
  echo json_encode($tableContents);
  exit();
}

// The tables header
$tableContents .= '<thead><tr><th>Access Level</th><th>Username</th><th>Name</th><th>Email</th><th>Last Changed Password</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {

  $columns = [
    // Access Level
    "<td>".formatColumn($record[0], 'N/A')."</td>",
    // Username
    "<td>".$record[1]."</td>",
    // Name
    "<td>".ucwords($record[2])." ".ucwords($record[3])."</td>",
    // Email
    "<td>".formatColumn($record[4], '-')."</td>",
    // Last Changed Password
    "<td>".formatColumn($record[5], 'Never')."</td>",
  ];

  // Appends this row to the table
  $tableContents .= formatRow($columns);
}

echo json_encode($tableContents);
?>
