<?php
// Include the idealink class or your database connection file
require_once("idlinkdependencies/idealnkconf_settings.php");

// Create an instance of the idealink class for the location table
$obj_location = new idealink("location");

// Get the location input from the POST request
$location = $_POST['location'];

// SQL query to search for locations that match the input
$sql = "SELECT locid, locname FROM location WHERE locname LIKE '%$location%' ORDER BY locname ASC";

// Execute the query
$res = $obj_location->query($sql);

// Check if results are found
if (mysqli_num_rows($res) > 0) {
    // Output the matching locations as suggestions
    while ($loc = mysqli_fetch_assoc($res)) {
        echo '<div class="location-suggestion" data-locid="' . $loc['locid'] . '">' . $loc['locname'] . '</div>';
    }
} else {
    // If no results, show a "no locations found" message
    echo '<div>No locations found</div>';
}
?>
