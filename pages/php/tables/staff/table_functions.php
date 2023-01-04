<?php
// This file contains useful functions required to format some of the data present in the staff tables
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Formats a row to be displayed in a table
function formatRow($columns=[]) {
  // Start of row
  $row = '<tr>';
  // Appends each column to the row
  foreach($columns as $column) {
    $row .= $column;
  }
  // End of row
  $row .= '</tr>';
  // Returns the formatted row
  return $row;
}
// Formats a column to be displayed in a table
function formatColumn($column, $nullText='-') {
    if (is_null($column) || empty($column)) {
      return $nullText;
    }
    else {
      return $column;
    }
}
// Formats a restricted users name in red
function formatRestricted($tmp) {
  if ($tmp == 1) {
    return " class='restricted'";
  }
  else {
    return "";
  }
}
// Formats a location name, replacing NULL locations with the prefered 'in' location
function formatLocation($location) {
  global $ini;
  if (is_null($location) || empty($location)) {
    return $ini['pref_in_location'];
  }
  else {
    return $location;
  }
}

?>
