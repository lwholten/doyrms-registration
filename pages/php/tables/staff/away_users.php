<?php
function fillAwayUsersTable() {
  // Connects to the database
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  // SQL code to get the table data
  $sql = 'SELECT Forename, Surname, LocationName, cast(TimeOut AS Date), cast(TimeOut AS Time), cast(TimeIn AS Date), cast(TimeIn AS Time), (CASE WHEN AwayUsers.UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted FROM AwayUsers LEFT JOIN Users ON Users.UserID = AwayUsers.UserID LEFT JOIN Locations ON Locations.LocationID = Users.LocationID ORDER BY AwayUsers.TimeIn, Users.Forename ASC LIMIT 100';
  // Saves the result of the SQL code to a variable
  $result = $con->query($sql);
  // Disconnects from the database
  $con->close();
  // Iterates through the table records and displays them on the web page's table
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Outputs data in the following order: fname, lname, locations, date signed out
    echo "<tr>";
    // Sets the font color depending on whether the user is restricted or not
    // fname, lname
    if ($record[7] === 1 || $record[7] === "1") {
      echo "<td class='restricted'>$record[0]</td><td class='restricted'>$record[1]</td>";
    }
    else {
      echo "<td>$record[0]</td><td>$record[1]</td>";
    }
    // locations, date signed out
    echo "<td>$record[2]</td><td>$record[3]</td><td>".substr($record[4], 0, 5)."</td>";
    // Outputs the date expected back or N/A if it has not been set
    if ($record[5] != NULL) {
      echo "<td>$record[4]</td><";
    }
    else {
      echo "<td>N/A</td>";
    }
    // Outputs the time expected back or N/A if it has not been set
    if ($record[6] != NULL) {
      echo "<td>".substr($record[6], 0, 5)."</td></tr>";
    }
    else {
      echo "<td>N/A</td></tr>";
    }
  }
}
?>
