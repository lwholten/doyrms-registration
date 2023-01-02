<?php
// This file contains code used to populate the 'away users' table on the staff page
// The table contents are json encoded and Ajax is used to regulary update the table without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Variables
$tableContents = '';

// Main
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get the table data
$sql = '(SELECT Forename, Surname, cast(DateTimeAway AS Date), cast(DateTimeReturn AS Date), cast(DateTimeReturn AS Time), Reason, (CASE WHEN AwayUsers.UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted FROM AwayUsers LEFT JOIN Users ON Users.UserID = AwayUsers.UserID ORDER BY AwayUsers.DateTimeReturn, Users.Forename ASC LIMIT 100)';
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
$tableContents .= '<thead><tr><th>Forename</th><th>Surname</th><th>Date signed out</th><th>Date expected back</th><th>At</th><th>Reason</th></tr></thead>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  // Outputs data in the following order: fname, lname, locations, date signed out
  $tableContents .= "<tr>";
  // Sets the font color depending on whether the user is restricted or not
  // fname, lname
  if ($record[6] === 1 || $record[6] === "1") {
    $tableContents .= "<td class='restricted'>$record[0]</td><td class='restricted'>$record[1]</td>";
  }
  else {
    $tableContents .= "<td>$record[0]</td><td>$record[1]</td>";
  }
  // The Date signed out
  $tableContents .= "<td>$record[2]</td>";
  // Outputs the date expected back or N/A if it has not been set
  if ($record[3] != NULL) {
    $tableContents .= "<td>$record[3]</td>";
  }
  else {
    $tableContents .= "<td>N/A</td>";
  }
  // Outputs the time expected back or N/A if it has not been set
  if ($record[4] != NULL) {
    $tableContents .= "<td>".substr($record[5], 0, 5)."</td>";
  }
  else {
    $tableContents .= "<td>N/A</td>";
  }
  // Outputs the time reason for the user being away
  if ($record[5] != NULL) {
    $tableContents .= "<td>$record[5]</td></tr>";
  }
  else {
    $tableContents .= "<td>None</td></tr>";
  }
}

echo json_encode($tableContents);
?>
