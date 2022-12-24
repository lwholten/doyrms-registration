<?php
// Fills the restricted users table
function fillRestrictedUsersTable() {
  // Connects to the database
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  // SQL code to get the table data
  $sql = "SELECT RestrictedUsers.UserID, Forename, Surname, Cast(TimeRestricted AS Date), TimeUnrestricted, RestrictedUsers.Description FROM RestrictedUsers LEFT JOIN Users ON Users.UserID = RestrictedUsers.UserID LEFT JOIN Locations ON Locations.LocationID = Users.LocationID ORDER BY RestrictedUsers.TimeUnrestricted, Users.Forename ASC LIMIT 100";
  // Saves the result of the SQL code to a variable
  $result = $con->query($sql);
  // Disconnects from the database
  $con->close();
  // Iterates through the table records and displays them on the web page's table
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    echo "<tr><td>$record[1]</td><td>$record[2]</td><td>$record[3]</td>";
    // Outputs the date expected back or N/A if it has not been set
    if ($record[4] != NULL) {

      // The date unrestricted
      echo "<td>".substr($record[4], 0, 10)."</td>";

      // Calculates the difference between the start and end times
      $currentDateTime = new DateTime();//start time
      $endDateTime = new DateTime(strval($record[4]));//end time
      $interval = $currentDateTime->diff($endDateTime);

      // An array of the time remaining in the format: time, suffix
      $timeRemaining = [
        // Note, ltrim() removes all leading 0's from the value, this prevents '05' hours etc.
        [ltrim(strval($interval->format("%m")), "0"), "Months "],
        [ltrim(strval($interval->format("%d")), "0"), "Days "],
        [ltrim(strval($interval->format("%H")), "0"), "Hours "],
        [ltrim(strval($interval->format("%i")), "0"), "Minutes "]
      ];

      // If the time difference is positive (before the end date)
      if (intval($interval->format("%R1")) > 0) {

        // removes (unsets) all elements contained within the array which has a value of 0 or NULL
        for ($i = 0; $i <= (count($timeRemaining)-1); $i++) {
          if (intval($timeRemaining[$i][0]) === 0 || $timeRemaining[$i][0] === NULL) {
            // This stops it from being output if the value is zero
            // e.g 0 Months, 0 Days, 5 Hours, 9 Minutes -> 5 Hours, 9 Minutes
            unset($timeRemaining[$i]);
          }
        }
        // Outputs the time remaining
        echo "<td>";
        $max = 0;
        foreach($timeRemaining as $value) {
          // Output the time and suffix
          echo "$value[0] $value[1]";
          // Limits it so only 2 may be present per record
          if (++$max == 2) break;
        }
        echo "</td>";

      }
      // If the time difference is not positive (past the end date)
      else {
        echo "<td>None</td>";
      }

    }
    else {
      echo "<td>N/A</td><td>-</td>";
    }
    // Outputs the description
    if ($record[5] != NULL) {
      echo "<td>$record[5]</td></tr>";
    }
    else {
      echo "<td>None</td></tr>";
    }
  }
}
?>
