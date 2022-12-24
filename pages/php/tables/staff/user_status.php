<?php
function fillUserStatusTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT (CASE WHEN UserID IN (SELECT UserID FROM AwayUsers) THEN 0 WHEN Users.LocationID IS NULL THEN 2 ELSE 1 END) AS Type, Forename, Surname, LocationName, cast(LastActive AS time), cast(LastActive AS date), (CASE WHEN UserID IN (SELECT UserID FROM RestrictedUsers) THEN 1 ELSE 0 END) AS Restricted FROM Users LEFT JOIN Locations ON Locations.LocationID = Users.LocationID ORDER BY Type, Users.Forename ASC LIMIT 100";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();
  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Iterates through each item of the record
    for ($i = 0; $i <= (count($record)-1); $i++) {
      if ($i === 0) {
        // 0 -> Away, 1 -> Out, 2 ->
        echo "<tr><td>";
        if ($record[$i] === 2 || $record[$i] === "0") {
          echo "<span class='inline-dot blue'></span> Away";
        }
        else if ($record[$i] === 0 || $record[$i] === "1") {
          echo "<span class='inline-dot red'></span> Out";
        }
        else if ($record[$i] === 1 || $record[$i] === "2") {
          echo "<span class='inline-dot green'></span> In";
        }
        else {
          echo "<span class='inline-dot orange'></span> N/A";
        }
        echo "</td>";
      }
      else if ($i === 1 || $i === 2) {
        // If the user is restricted, change the font color to red
        if ($record[6] === 1 || $record[6] === "1") {
          echo "<td class='restricted'> $record[$i] </td>";
        }
        else {
          echo "<td> $record[$i] </td>";
        }
      }
      // 3 is the index of the 'location' column, if it is set to NULL, the user is signed in
      else if ($i === 3) {
        if ($record[$i] === "0" || $record[$i] === 0 || $record[$i] === NULL) {
          echo "<td>The Boarding House</td>";
        }
        else {
          echo "<td>$record[$i]</td>";
        }
      }
      else if ($i === 4) {
        // We only need the time in format: HH:MM
        $time = substr($record[$i], 0, 5);
        echo "<td>$time</td>";
      }
      // Skips the final column
      else if ($i === 6) {
        continue;
      }
      // If it is any other column
      else {
        echo "<td> $record[$i] </td>";
      }
    };
  }
}
?>
