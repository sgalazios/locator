<?php

include 'db_inc.php';

$input = file_get_contents('php://input');
$data = explode("|", $input);
$scanner = $data[0];
$scans = $data[1];

// Remove start & end markers and split each found BLE device
$scans = str_replace("OK+DISIS", "", $scans);
$scans = str_replace("OK+DISCE", "", $scans);
$devices = explode('OK+DISC:', $scans);

// Get scanner ID from database
$query = "SELECT id FROM scanners WHERE mac LIKE '$scanner';";
$result = mysqli_query($mysqli, $query);

if(mysqli_num_rows($result) > 0 ){
    $row = mysqli_fetch_assoc($result);
    $scanner_id =  $row['id'];
}

foreach ($devices as $key=>$dev) {
    if($dev != ""){
        $props = explode(':', $dev);
        $UUID = $props[1];

        // Ignore other BLE devices
        if($UUID != "27AEF06B2A6649CEBFEC069923379DF7") continue;

        $mj_mn = $props[2];
        $major = hexdec(substr($mj_mn, 0, 4));
        $minor = hexdec(substr($mj_mn, 4, 4));
        $MAC   = $props[3];
        $RSSI  = $props[4];
        $RSSI  = intval(substr($RSSI, 0, 4)); // Trim to 4 characters to eliminate partial OK+DISCE delimiters

        // Get beacon ID from database
        $query = "SELECT id FROM beacons WHERE major = '$major' AND minor = '$minor';";
        $result = mysqli_query($mysqli, $query);

        if(mysqli_num_rows($result) > 0 ){
            $row = mysqli_fetch_assoc($result);
            $beacon_id = $row['id'];

            // Check if scanned recently by other scanners with lower enough RSSI and delete those records
            // as they are probably nearby scanners
            $del_query = "DELETE FROM discovers
                            WHERE beacon_id = '$beacon_id'
                            AND scanner_id != '$scanner_id'
                            AND timestamp > date_sub(now(), interval 3 minute)
                            AND ((rssi + rssi_2 + rssi_3)/3) <= ($RSSI - 15);";
            mysqli_query($mysqli, $del_query);
        
            // Check if this scanner has already seen the beacon and update that record instead
            $query = "SELECT id, rssi, rssi_2, rssi_3 FROM discovers WHERE scanner_id = '$scanner_id' AND beacon_id = $beacon_id;";
            $result = mysqli_query($mysqli, $query);

            if(mysqli_num_rows($result) > 0 ){
                $row = mysqli_fetch_assoc($result);
                $disc_id = $row['id'];
                $rssi_2 = $row['rssi'];
                $rssi_3 = $row['rssi_2'];

                // Store current RSSI and move previous values by 1
                $upd_query = "UPDATE discovers SET rssi = '$RSSI', rssi_2 = '$rssi_2', rssi_3 = '$rssi_3' WHERE id = '$disc_id';";
                mysqli_query($mysqli, $upd_query);
                echo "Updated existing record<br>";

            } else {
                $ins_query = "INSERT INTO discovers (scanner_id, beacon_id, rssi, rssi_2, rssi_3) VALUES ('$scanner_id', '$beacon_id', '$RSSI', '$RSSI', '$RSSI');";
                mysqli_query($mysqli, $ins_query);
            }
        }
    }
}

?>