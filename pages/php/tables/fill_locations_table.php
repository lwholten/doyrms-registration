<?php
function fillLocationsTable() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  $sql = "SELECT * FROM `Locations`";
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
    /*Makes the edit section appear for the selected record, passes the record id and location name as parameters*/
    /*record[0] --> The ID of that record | record[1] --> The name of that location*/
    echo "<td><button onclick=\"formatEditSection('locations_edit_section','location_id_storage','$record[1]','$record[0]');\" class=\"blue_button edit_button\">Edit</button></td>";
    echo "</tr>";
  }
}
?>
