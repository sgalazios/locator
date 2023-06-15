<?php

include 'db_inc.php';

$search = $_GET['search'];
$action_target = $_GET['action_target'];

$data = explode("-", $action_target);
$action = $data[0];
$target = $data[1];

if ($action && $target) {
    if ($action == "lock") {
        $query = "UPDATE beacons SET in_use = 1 WHERE id = $target;";
    } else if ($action == "unlock") {
        $query = "UPDATE beacons SET in_use = 0 WHERE id = $target;";
    }
    mysqli_query($mysqli, $query);
}

header("Location: results.php?beacon=" . $search);
exit();

?>