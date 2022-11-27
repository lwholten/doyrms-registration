<?php
function fillEventsTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT * FROM `Events`";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();

  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    echo "<tr>";
    // Iterates through each item of the record
    for ($i = 0; $i <= (count($record)-1); $i++) {
      // 4 is the index for the 'Days' column
      // Rather of displaying the numerical value of this column
      // It will be displaying the days that correspond to the numerical value
      if ($i === 4) {
        echo "<td>";
        // Note, this could be made more efficient using a 'for' loop - maybe implement this in the future
        $dec = $record[$i];
        if ($dec >= 64) {
          echo "Mon, ";
          $dec -= 64;
        }
        if ($dec >= 32) {
          echo "Tue, ";
          $dec -= 32;
        }
        if ($dec >= 16) {
          echo "Wed, ";
          $dec -= 16;
        }
        if ($dec >= 8) {
          echo "Thu, ";
          $dec -= 8;
        }
        if ($dec >= 4) {
          echo "Fri, ";
          $dec -= 4;
        }
        if ($dec >= 2) {
          echo "Sat, ";
          $dec -= 2;
        }
        if ($dec >= 1) {
          echo "Sun, ";
          $dec -= 1;
        }
        echo "</td>";
      }
      // 5 is the index for the 'Alerts' column
      else if ($i === 5) {
        echo "<td>";
        if ($record[$i] === "1") {
          echo "Yes";
        }
        else if ($record[$i] === "0") {
          echo "No";
        }
        else {
          echo "N/A";
        }
        echo "</td>";
      }
      // If it is any other column
      else {
        echo "<td> $record[$i] </td>";
      }
    };
    /*Makes the edit section appear for the selected record, passes the record id and location name as parameters*/
    /*record[0] --> The ID of that record | record[1] --> The name of that location*/
    echo "<td><button onclick=\"formatEditSection('events_edit_section','event_id_storage','$record[1]','$record[0]');\" class=\"blue_button edit_button\">Edit</button></td>";
    echo "</tr>";
  }
}
?>