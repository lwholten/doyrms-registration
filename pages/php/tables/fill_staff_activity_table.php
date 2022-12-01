<?php
function fillStaffActivityTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT Username, cast(SignInTime AS date), cast(SignInTime AS time), cast(SignOutTime AS date), cast(SignOutTime AS Time) FROM StaffLog INNER JOIN Staff ON StaffLog.StaffID = Staff.StaffID WHERE Complete=1 ORDER BY SignInTime DESC";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();

  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    echo "<tr>";
    foreach ($record as $value) {
      if ($value === NULL || $value === None || $value === 0) {
        echo "<td> N/A </td>";
      }
      else {
        echo "<td> $value </td>";
      }
    }
  }
}
?>
