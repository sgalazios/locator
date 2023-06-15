<?php

    header('Content-type: text/plain; charset=utf-8');

    include 'db_inc.php';

    $input = file_get_contents('php://input');  // MAC Address of beacon; no colons

    $query = "SELECT descr, major, minor FROM beacons WHERE mac LIKE '%$input%';";
    $result = mysqli_query($mysqli, $query);
    if($result->num_rows == 0) echo "NOT_FOUND";
    while($row = mysqli_fetch_assoc($result)) {
        echo $row['major'], '|', $row['minor'], '|', $row['descr'];
    }

?>