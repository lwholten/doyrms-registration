<?php
// This file contains code used to populate the 'away users' table on the staff page
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
$sql = '(SELECT Forename, Surname, cast(DateTimeAway AS Date), cast(DateTimeAway AS Time), cast(DateTimeReturn AS Date), cast(DateTimeReturn AS Time), Reason, (CASE WHEN AwayUsers.UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted FROM AwayUsers LEFT JOIN Users ON Users.UserID = AwayUsers.UserID ORDER BY AwayUsers.DateTimeReturn, Users.Forename ASC LIMIT 100)';
// Saves the result of the SQL code to a variable
$result = $con->query($sql);
// Disconnects from the database
$con->close();

// If the table is empty, echo the placeholder for an empty table
if (!(mysqli_num_rows($result) > 0)) {
  $tableContents = "<div class='empty_table_placeholder'><h3>Hmm...</h3><text>¯\_(ツ)_/¯</text><h3>...It seems that no one is away at the moment</h3></div>";
  echo json_encode($tableContents);
  exit();
}

// The tables header
$tableContents .= '<thead><tr><th>User</th><th>Date marked away</th><th>At</th><th>Date expected back</th><th>At</th><th>Reason</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {

  $columns = [
    // User
    "<td".formatRestricted($record[7]).">".$record[0]." ".$record[1]."</td>",
    // Date marked away
    "<td>$record[2]</td>",
    // Time marked away
    "<td>".formatColumn(substr($record[3], 0, 5), '-')."</td>",
    // Date expected back
    "<td>".formatColumn($record[4], 'Not Specified')."</td>",
    // Time expected back
    "<td>".formatColumn(substr($record[5], 0, 5), '-')."</td>",
    // Reason
    "<td>".formatColumn($record[6], 'None')."</td>",
  ];

  // Appends this row to the table
  $tableContents .= formatRow($columns);
}

echo json_encode($tableContents);
?>
