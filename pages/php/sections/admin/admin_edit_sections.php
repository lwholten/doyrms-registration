<?php
// This file contains code used to load the admin edit sections to the page

// Array to contain the paths containing each sections respective HTML code
$sectionPaths = [
    "edit_staff_section" => "../../../html/admin_sections/edit_staff_section.html",
    "edit_users_section" => "../../../html/admin_sections/edit_users_section.html",
    "edit_locations_section" => "../../../html/admin_sections/edit_locations_section.html",
    "edit_events_section" => "../../../html/admin_sections/edit_events_section.html",
];

foreach ($sectionPaths as $key => $path) {
    include($sectionPaths[$key]);
}
?>