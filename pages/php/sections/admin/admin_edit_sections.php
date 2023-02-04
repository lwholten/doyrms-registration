<?php
// This file contains code used to load the admin edit sections to the page

// Makes this file only executable if the signed in staff user has a high enough access level
session_start();
if ($_SESSION['staffAccessLevel'] >= 3) {

    // Array to contain the paths containing each sections respective HTML code
    $sectionPaths = [
        "edit_staff_section" => "../../../html/admin_sections/edit_staff_section.html",
        "edit_users_section" => "../../../html/admin_sections/edit_users_section.html",
        "edit_locations_section" => "../../../html/admin_sections/edit_locations_section.html",
        "edit_events_section" => "../../../html/admin_sections/edit_events_section.html",
    ];

    // Includes the link to the admin js file
    include('../../../html/admin_sections/script_link.html');
    // Includes each admin section
    foreach ($sectionPaths as $key => $path) {
        include($sectionPaths[$key]);
    }
    
}
?>