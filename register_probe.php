<?php

include 'db_inc.php';

$input = file_get_contents('php://input');
$input = str_replace(array("\n","\r"), '', $input); // Remove newline terminators in user input
$data = explode("|", $input);

$mac = $data[0];
$location = $data[1];

if ($mac && $location) {

    // Check if scanner already in DB and update it
    $query = "SELECT id FROM scanners WHERE mac = '$mac';";
    $result = mysqli_query($mysqli, $query);

    if(mysqli_num_rows($result) > 0 ){
        $row = mysqli_fetch_assoc($result);
        $query = "UPDATE scanners SET location = '$location' WHERE mac = '$mac';";
        mysqli_query($mysqli, $query);
    } else {
        // Register new scanner
        $query = "INSERT INTO scanners (location, mac) VALUES ('$location', '$mac');";
        mysqli_query($mysqli, $query);
    }
    
}

?>