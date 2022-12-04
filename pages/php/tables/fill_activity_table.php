<?php
function fillActivityTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT (CASE WHEN Users.LocationID IS NULL THEN 1 ELSE 0 END), Forename, Surname, LocationName, cast(LastActive AS time), cast(LastActive AS date) FROM Users LEFT JOIN Locations ON Locations.LocationID = Users.LocationID";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();
  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    echo "<tr>";
    // Iterates through each item of the record
    for ($i = 0; $i <= (count($record)-1); $i++) {
      if ($i === 0) {
        echo "<td>";
        if ($record[$i] === 1 || $record[$i] === "1") {
          echo "<span class='inline-dot green'></span> In";
        }
        else if ($record[$i] === 0 || $record[$i] === "0") {
          echo "<span class='inline-dot red'></span> Out";
        }
        else {
          echo "<span class='inline-dot orange'></span> N/A";
        }
        echo "</td>";
      }
      // 2 is the index of the 'location' column, if it is set to NULL, the user is signed in
      else if ($i === 3) {
        if ($record[$i] === "0" || $record[$i] === 0 || $record[$i] === NULL) {
          echo "<td>The Boarding House</td>";
        }
        else {
          echo "<td>$record[$i]</td>";
        }
      }
      // If it is any other column
      else {
        echo "<td> $record[$i] </td>";
      }
    };
  }
}
?>
