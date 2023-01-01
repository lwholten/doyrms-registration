<?php
// This file contains code used to populate the 'restricted users' table on the staff page
// The table contents are json encoded and Ajax is used to regulary update the table without refreshing the page

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Variables
$tableContents = '';

// Main
// Connects to the database
$con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
// SQL code to get the table data
$sql = "SELECT RestrictedUsers.UserID, Forename, Surname, Cast(DateTimeRestricted AS Date), DateTimeUnrestricted, RestrictedUsers.Reason FROM RestrictedUsers LEFT JOIN Users ON Users.UserID = RestrictedUsers.UserID LEFT JOIN Locations ON Locations.LocationID = Users.LocationID ORDER BY RestrictedUsers.DateTimeUnrestricted, Users.Forename ASC LIMIT 100";
// Saves the result of the SQL code to a variable
$result = $con->query($sql);
// Disconnects from the database
$con->close();

// If the table is empty, echo the placeholder for an empty table
if (!(mysqli_num_rows($result) > 0)) {
  $tableContents = "<div class='empty_table_placeholder'><h3>Hmm...</h3><text>¯\_(ツ)_/¯</text><h3>...There seem to be no restricted users</h3></div>";
  echo json_encode($tableContents);
  exit();
}

// The table header and start of the table body
$tableContents .= '<thead><tr><th>Forename</th><th>Surname</th><th>Date Restricted</th><th>Until</th><th>Time Remaining</th><th>Reason</th></tr></thead><tbody>';
// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  $tableContents .= "<tr><td class='restricted'>$record[1]</td><td class='restricted'>$record[2]</td><td>$record[3]</td>";
  // Appends the date expected back or N/A if it has not been set
  if ($record[4] != NULL) {
    // The date unrestricted
    $tableContents .= "<td>".substr($record[4], 0, 10)."</td>";
    // Calculates the difference between the start and end times
    $currentDateTime = new DateTime();//start time
    $endDateTime = new DateTime(strval($record[4]));//end time
    $interval = $currentDateTime->diff($endDateTime);
    // An array of the time remaining in the format: time, suffix
    $timeRemaining = [
      // Note, ltrim() removes all leading 0's from the value, this prevents '05' hours etc.
      [strval($interval->format("%m")), "Months "],
      [strval($interval->format("%d")), "Days "],
      [strval($interval->format("%H")), "Hours "],
      [strval($interval->format("%i")), "Minutes "],
      [strval($interval->format("%s")), "Seconds "]
    ];
    // If the time difference is positive (before the end date)
    // (uses the time to create a +1 or -1)
    if (intval($interval->format("%R1")) > 0) {
        // removes (unsets) all elements contained within the array which has a value of 0 or NULL
      for ($i = 0; $i <= (count($timeRemaining)-1); $i++) {
        if (strval($timeRemaining[$i][0]) === "0" || strval($timeRemaining[$i][0]) === "00" || $timeRemaining[$i][0] === NULL) {
          // This stops it from being output if the value is zero
          // e.g 0 Months, 0 Days, 5 Hours, 9 Minutes -> 5 Hours, 9 Minutes
          unset($timeRemaining[$i]);
        }
      }
      // Appends the time remaining
      $tableContents .= "<td>";
      $max = 0;
      foreach($timeRemaining as $value) {
        // Output the time and suffix
        if (strval($value[0]) === "0" || strval($value[0]) === "00") {
          continue;
        } else {
          $tableContents .= "$value[0] $value[1]";
        }
        // Limits it so only 2 may be present per record
        if (++$max == 2) break;
      }
      $tableContents .= "</td>";
      }
    // If the time difference is not positive (past the end date)
    else {
      $tableContents .= "<td>None</td>";
    }
  }
  else {
    $tableContents .= "<td>N/A<td>";
  }
  // Appends the description
  if ($record[5] != NULL) {
    $tableContents .= "<td>$record[5]</td></tr>";
  }
  else {
    $tableContents .= "<td>None</td></tr>";
  }
}

// Ends the body
$tableContents .= "</tbody>";
echo json_encode($tableContents);
?>
