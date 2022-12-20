<?php
function loadEventCards() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get all the event data (maximum of 25 events)*/
  $sql = "SELECT Event, LocationName, StartTime, EndTime, Deviation, Days, Alerts FROM Events INNER JOIN Locations ON Events.LocationID = Locations.LocationID UNION SELECT Event, LocationID, StartTime, EndTime, Deviation, Days, Alerts FROM Events WHERE LocationID IS NULL ORDER BY StartTime ASC LIMIT 25";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();

  // Creates an array containing all of the background gradients
  // Indexing this array gived the gradient that corresponds to the time of the index + 1 (e.g. for 12PM, array[13])
  $backgrounds = [
    'linear-gradient(#012459 0%, #001322 100%)',
    'linear-gradient(#012459 0%, #001323 100%)',
    'linear-gradient(#003972 0%, #001322 100%)',
    'linear-gradient(#004372 0%, #00182b 100%)',
    'linear-gradient(#004372 0%, #011d34 100%)',
    'linear-gradient(#016792 0%, #00182b 100%)',
    'linear-gradient(#07729f 0%, #042c47 100%)',
    'linear-gradient(#12a1c0 0%, #07506e 100%)',
    'linear-gradient(#74d4cc 0%, #1386a6 100%)',
    'linear-gradient(#efeebc 0%, #61d0cf 100%)',
    'linear-gradient(#fee154 0%, #a3dec6 100%)',
    'linear-gradient(#fdc352 0%, #e8ed92 100%)',
    'linear-gradient(#ffac6f 0%, #ffe467 100%)',
    'linear-gradient(#fda65a 0%, #ffe467 100%)',
    'linear-gradient(#fd9e58 0%, #ffe467 100%)',
    'linear-gradient(#f18448 0%, #ffd364 100%)',
    'linear-gradient(#f06b7e 0%, #f9a856 100%)',
    'linear-gradient(#ca5a92 0%, #f4896b 100%)',
    'linear-gradient(#5b2c83 0%, #d1628b 100%)',
    'linear-gradient(#371a79 0%, #713684 100%)',
    'linear-gradient(#28166b 0%, #45217c 100%)',
    'linear-gradient(#192861 0%, #372074 100%)',
    'linear-gradient(#040b3c 0%, #233072 100%)',
    'linear-gradient(#040b3c 0%, #012459 100%)',
  ];

  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Gets the starting hour of the event and stores it as an integer (e.g. 12 PM as 12, 4PM as 16 etc.)
    $hour = intval(substr($record[3], 0, 2));
    // Sets the card background to the background corresponding to this integer
    $cardBackground = $backgrounds[$hour];
    // Creates the 'card' for this event using a list item
    echo "<li style='background: $cardBackground;'><div class='text event_card'>";
      echo "<header style='color: white;'>";
        echo "<h2>$record[0]</h2>";
      echo "</header>";
      echo "<article>";
        // Outputs the location name
        echo "<h3>$record[1]</h3>";
        // Title for the days column
        echo "<h3>Days</h3>";
        // Outputs the times that the event is active
        $start = substr($record[2], 0, 5);
        $end = substr($record[3], 0, 5);
        echo "<div class='content'><h4>Start</h4><h4>End</h4><h3>$start</h3><h3>$end</h3><h4>Notes</h4></div>";
        unset($start, $end);
        // Outputs a list containing every day of the week with a bar based on whether it is enabled or not
        $days = [
          'M' => 'Monday',
          'T' => 'Tuesday',
          'W' => 'Wednesday',
          'R' => 'Thursday',
          'F' => 'Friday',
          'U' => 'Saturday',
          'S' => 'Sunday'
        ];
        echo "<ul class='days'>";
        // For every day, if the event contains the days corresponding letter, display a true
        foreach(str_split('MTWRFUS') as $day){
          echo "</li><h4><span class='inline-bar ";
          if (str_contains($record[5], $day)) {
            echo "green";
          }
          else {
            echo "red";
          };
          // Outputs the day that corresponds to the days letter
          echo "'></span>".$days[$day]."</h4></li>";
        }
        echo "</ul>";
        // Outputs a break to push the alerts to next next column
        if (intval($record[4]) === 0) {
          echo "<h5>Users must be on time</h5>";
        }
        else {
          echo "<h5>Users may $record[4] minutes late/early</h5>";
        }
        // Outputs the alert status
        if ($record[6] === '1') {
          echo "<h4 class='alerts'><span class='inline-bar green'></span>Alerts Enabled</h4>";
        }
        elseif ($record[6] === '0') {
          echo "<h4 class='alerts'><span class='inline-bar red'></span>Alerts Disabled</h4>";
        }
        else {
          echo "<h4 class='alerts'><span class='inline-bar orange'></span>Alerts Unknown</h4>";
        }
      echo "</article>";
    echo "</div></li>";
  }
}
?>
