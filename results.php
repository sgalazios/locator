<!DOCTYPE html>
<html lang="el">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Αποτελέσματα αναζήτησης</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        <script src="jquery.min.js"></script>
        <script src="jquery.timeago.js"></script>

        <script>
		jQuery(document).ready(function() {
			jQuery("time.timeago").timeago();
		});
	    </script>

        <style>
            html,
            body,
            .search-box {
            height: 100%;
        }
        </style>
</head>

<body>

    <div class="valign-wrapper row search-box">
    <div class="col card hoverable s10 pull-s1 m6 pull-m3 l4 pull-l4">
        <div class="card-content">
            <span class="card-title">Αποτελέσματα αναζήτησης</span>
            <div class="row">
            </div>

            <?php

            include 'db_inc.php';

            if(isset($_GET['beacon']) && $_GET['beacon'] != ""){

                $beacon_target = rtrim($_GET['beacon']);

                // Get beacons' IDs that match the search term
                $ids = array();
                $query = "SELECT id FROM beacons WHERE descr LIKE '%$beacon_target%';";
                $result = mysqli_query($mysqli, $query);

                while($row = mysqli_fetch_assoc($result)) {
                    array_push($ids, $row['id']);
                }

                if (empty($ids)) echo "Δεν βρέθηκε αντικείμενο με αυτό το όνομα.";

                // Check where each beacon has been discovered
                foreach($ids as $id){
                    $printed_title = false; // Print beacon's name only once per beaacon
                    
                    // Check where each beacon ID has been discovered
                foreach($ids as $id){
                    $printed_title = false; // To print beacon's name only once per beaacon
                    
                    // Get latest positions - Sort on RSSI - Filter top 3 results
                    $query =   "SELECT descr, location, rssi, rssi_2, rssi_3, timestamp, in_use
                                FROM discovers
                                INNER JOIN beacons on beacons.id = $id
                                INNER JOIN scanners ON scanner_id = scanners.id
                                WHERE beacon_id = $id
                                AND timestamp > date_sub(now(), interval 60 minute)
                                ORDER BY rssi DESC
                                LIMIT 3;";

                    // Save data into an array
                    $result = mysqli_query($mysqli, $query);
                    $rows = array();
                    while ($row = mysqli_fetch_assoc($result)) {
                        $rows[] = $row;
                    }

                    // Set RSSI threshold to 10 dBm lower than the highest scanned RSSI
                    if ($rows) {
                        $threshold = intval($rows[0]['rssi']) - 10;
                    }

                    // Print title and 'Reserved' status and button
                    foreach ($rows as $row) {
                        if(!$printed_title){
                            echo "<div class=\"row\">\n";
                            if ($row['in_use']) echo "🔒";
                            echo "<b>", $row['descr'], "</b> ";
                            if ($row['in_use']){
                                echo "<td><a id=\"unlock-", $id , "\" class=\"waves-effect waves-light btn-small\">🔓 Αποδέσμευση</a>";
                            } else {
                                echo "<td><a id=\"lock-", $id , "\" class=\"waves-effect waves-light btn-small\">🔒 Δέσμευση</a>";
                            }
                            echo "<hr><table>\n";
                            $printed_title = true;
                        }
                        echo "<tr>\n";

                        // Print result if high enough RSSI
                        if (intval($row['rssi']) > $threshold) {
                            echo "<td>", $row['location'], "</td>\n";
                            $RSSI = intval($row['rssi']);
                            $RSSI_2 = intval($row['rssi_2']);
                            $RSSI_3 = intval($row['rssi_3']);
                            $RSSIavg = ($RSSI + $RSSI_2 + $RSSI_3) / 3; 
                            $RSSI1m = -54;  // RSSI at 1 meter distance
                            $n = 2;         // path loss exponent
                            $d = pow(10, ($RSSI1m - $RSSIavg) / (10 * $n));
                            echo "<td>~", round($d), " μέτρα</td>\n";
                                                    
                            // Print timestamp in a friendly relative format
                            echo "<td><time class=\"timeago\" datetime=\"", $row['timestamp'], "\">";
                            echo $row['timestamp'], "</time></td>\n";
                            echo "</tr>\n";
                        }
                        
                    }
                    if ($printed_title) echo "</table>\n</div>\n";
                }
            } else {
                echo "Δεν δόθηκε παράμετρος αναζήτησης";
            }

            ?>

        <div class="card-action right-align">
            <button class="waves-effect waves-teal btn-flat" onClick="window.location.reload();" name="reset">Ανανέωση</button>
            <button class="btn waves-effect waves-light" onclick="window.location='search.php';" name="newsearch">Νέα Αναζήτηση</button>
        </div>
        <script>
            $("a").click(function() {
                let getUrlParameter = function getUrlParameter(sParam) {
                    let sPageURL = window.location.search.substring(1),sURLVariables = sPageURL.split('&'),sParameterName,i;

                    for (i = 0; i < sURLVariables.length; i++) {
                        sParameterName = sURLVariables[i].split('=');

                        if (sParameterName[0] === sParam) {
                            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                        }
                    }
                    return false;
                };
                let searchTerm = getUrlParameter("beacon");
                location.href = `lock_unlock.php?search=${searchTerm}&action_target=${this.id}`;
            });
        </script>
    </div>
    </div>
</body>
</html>
