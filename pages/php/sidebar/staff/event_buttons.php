<?php
function loadSidebarEventButtons() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get all the event data (maximum of 25 events)*/
  $sql = "SELECT Event FROM Events";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();

  // For each event from the database, echo a button which leads to its section
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // generates an id for the section
    $section_id = strtolower("$record[0]_event_section");
    // generates an id for the button
    $button_id = strtolower("$record[0]_event_button");
    // generates the text displayed for the button
    $name = ucwords(strtolower($record[0]));
    // echos the generated button
    echo "<li onclick='toggleMainSection(this.id,`$section_id`)' class='sidebar_button' id='$button_id'><h4>$name</h4></li>";
  }
}
?>
