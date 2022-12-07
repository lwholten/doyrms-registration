<?php
function fillStaffStatusTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT Active, Username, AccessLevel, cast(LastActive AS time), cast(LastActive AS date) FROM Staff ORDER BY Staff.Active DESC, Staff.Username ASC LIMIT 100";
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
        if ($record[$i] === 0 || $record[$i] === "0") {
          echo "<span class='inline-dot red'></span> Offline";
        }
        else if ($record[$i] === 1 || $record[$i] === "1") {
          echo "<span class='inline-dot green'></span> Online";
        }
        else {
          echo "<span class='inline-dot orange'></span> N/A";
        }
        echo "</td>";
      }
      // 2 is the index of the 'access level' column, we are replacing the number with its corresponding access level
      else if ($i === 2) {
        if ($record[$i] === "1" || $record[$i] === 1) {
          echo "<td>Staff</td>";
        }
        else if ($record[$i] === "2" || $record[$i] === 2) {
          echo "<td>Moderator</td>";
        }
        else if ($record[$i] === "3" || $record[$i] === 3) {
          echo "<td>Administrator</td>";
        }
        else {
          echo "<td>Unknown</td>";
        }
      }
      else if ($i === 3) {
        // We only need the time in format: HH:MM
        $time = substr($record[$i], 0, 5);
        echo "<td>$time</td>";
      }
      // If it is any other column
      else {
        echo "<td> $record[$i] </td>";
      }
    };
  }
}
?>
