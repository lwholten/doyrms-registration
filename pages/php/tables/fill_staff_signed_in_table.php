<?php
function fillStaffSignedInTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT Username, cast(SignInTime AS date), cast(SignInTime AS time) FROM StaffLog INNER JOIN Staff ON StaffLog.StaffID = Staff.StaffID WHERE Complete=0 ORDER BY SignInTime DESC";
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
