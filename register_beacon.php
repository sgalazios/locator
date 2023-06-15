<?php

include 'db_inc.php';

$input = file_get_contents('php://input');
$data = explode("|", $input);

$major = $data[0];
$mac = $data[1];

if ($major && $mac) {

    // Check if beacons for this Major exist, else return 1 as the first Minor
    $query = "SELECT * FROM beacons WHERE major = '$major';";
    $result = mysqli_query($mysqli, $query);

    if(mysqli_num_rows($result) == 0 ){
        $minor = 1;

        // Register new beacon
        $name = "Beacon " . $minor;
        $query = "INSERT INTO beacons (descr, mac, major, minor) VALUES ('$name', '$mac', '$major', '$minor');";
        mysqli_query($mysqli, $query);
        echo $minor;
    } else {
    
        // Check if beacon already in DB and return its Minor
        $query = "SELECT minor FROM beacons WHERE mac = '$mac' AND major = '$major';";
        $result = mysqli_query($mysqli, $query);

        if(mysqli_num_rows($result) > 0 ){
            $row = mysqli_fetch_assoc($result);
            $minor = $row['minor'];
        } else {
            // Get next available Minor for this Major
            $query = "SELECT MAX(minor) AS MaxMinor FROM beacons WHERE major = $major;";
            $result = mysqli_query($mysqli, $query);
            if(mysqli_num_rows($result) > 0 ){
                $row = mysqli_fetch_assoc($result);
                $minor =  $row['MaxMinor'] + 1;
            }

            // Register new beacon
            $name = "Beacon " . $minor;
            $query = "INSERT INTO beacons (descr, mac, major, minor) VALUES ('$name', '$mac', '$major', '$minor');";
            mysqli_query($mysqli, $query);
        }

        // Return minor to be read by ESP to config the module
        echo $minor;
    }
}

?>