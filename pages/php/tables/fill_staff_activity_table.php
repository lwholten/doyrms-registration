<?php
function fillStaffActivityTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT StaffLogID, Username, cast(DateTime AS date), cast(DateTime AS Time) FROM StaffLog INNER JOIN Staff ON StaffLog.StaffID = Staff.StaffID ORDER BY DateTime DESC";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();

  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    echo "<tr>";
    foreach ($record as $value) {
      echo "<td> $value </td>";
    }
  }
}
?>
