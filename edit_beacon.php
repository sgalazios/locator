<?php

    header('Content-type: text/plain; charset=utf-8');

    include 'db_inc.php';

    $input = file_get_contents('php://input');  //Beacon MAC and new name, separated with '|'
    $data = explode("|", $input);

    $mac = $data[0];
    $name = $data[1];

    if ($mac && $name) {
        $query = "UPDATE beacons SET descr = '$name' WHERE mac = '$mac';";
        mysqli_query($mysqli, $query);
        echo "NAME_SET_SUCCESS";
    }

?>