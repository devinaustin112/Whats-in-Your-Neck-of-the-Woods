<?php
    include "Database.php";
    include 'query.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!-- link for leaflet map -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
              integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
              crossorigin=""/>
        <!-- script for leaflet map-->
        <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
                integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
        crossorigin=""></script>
    </head>

    <body class="chart">
        <table  border="1" cellpadding="3">
            <h3 style='font-family: Georgia, serif;'>Safest Counties Per Location Type</h3>
            <tr>
                <td colspan="3" bgcolor="lightyellow">
                    <form method="GET">
                        <select name="location" onchange="this.form.submit()"> 
                            <option></option>
                            <?php
                                $rws = query::get_all_location_types();
                                while ($row = pg_fetch_row($rws)) {
                                    echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
                                }
                            ?>

                        </select>
                    </form>
                    <?php
                        if (!empty($_GET["location"])) {
                            echo "Results shown for: " . $_GET["location"];
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Station</th>
                <th>Murder Count</th>
                <td rowspan="12">
                    <div id="map_div" style="width: 500px; height: 500px;"></div>

                </td>
            </tr>
            <?php
                if (!empty($_GET["location"])) {
                    $location = $_GET["location"];
                } else {
                    $location = "art";
                }
                $rs = query::safest_counties_per_location_type($location, 2014);
                while ($row = pg_fetch_row($rs)) {
                    echo "<tr><td>" . $row[0] . "</td>";
                    echo "<td>" . $row[3] . "</td></tr>";
                }
            ?>

        </table>  

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
    

            var mymap = L.map('map_div').setView([38.4496, -78.8689], 6);


            var layerGroup = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiZGV2aW5hdXN0aW4xMTIiLCJhIjoiY2p1dnlmYzR0MDZ0cDRka2lvMzIydGJ6ZyJ9.xtNhagSY6F3jzhsHvfyjKg', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox.streets',
                accessToken: 'your.mapbox.access.token'
            }).addTo(mymap);
<?php
            if (!empty($_GET["location"])) {
                $location = $_GET["location"];
            } else {
                $location = "art";
            }
            $rs = query::safest_counties_per_location_type($location, 2014);
            while ($row = pg_fetch_row($rs)) {
                echo 'L.marker([' . $row[1] . ',' . $row[2] . ']).addTo(mymap).bindPopup("' . $row[0] . '");';
            }
?>
        </script>
    </body>
</html>