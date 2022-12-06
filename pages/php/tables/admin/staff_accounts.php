<?php
function loadStaffAccountsTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT Staff.StaffID, Staff.Username, Staff.AccessLevel, Staff.Forename, Staff.Surname, Staff.Email FROM Staff LIMIT 100";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();

  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    echo "<tr>";
    // Iterates through each item of the record
    for ($i = 0; $i <= (count($record)-1); $i++) {
      if ($i === 2) {
        echo "<td>";
        if ($record[$i] === 3 || $record[$i] === "3") {
          echo "Administrator";
        }
        else if ($record[$i] === 2 || $record[$i] === "2") {
          echo "Moderator";
        }
        else if ($record[$i] === 1 || $record[$i] === "1") {
          echo "Staff";
        }
        else {
          echo "Unknown";
        }
        echo "</td>";
      }
      // If it is any other column
      else {
        echo "<td> $record[$i] </td>";
      }
    };
    /*Makes the edit section appear for the selected record, passes the record id and user forename + surname as parameters*/
    /*record[0] --> The ID of that record | record[1] --> The name of that location*/
    echo "<td><button onclick=\"formatEditSection('staff_edit_section','staff_id_storage','$record[1]','$record[0]');\" class=\"blue_button edit_button\">Edit</button></td>";
  }
}
?>
