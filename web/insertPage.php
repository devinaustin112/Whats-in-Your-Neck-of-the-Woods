<?php
            include "Database.php";
            include 'query.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Insert</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
              crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="w3.css">
        <link rel="icon" href="tree.png">

        <!-- link for leaflet map -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
              integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
              crossorigin=""/>
        <!-- script for leaflet map-->
        <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
                integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
        crossorigin=""></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>  
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <!--    responsive top nav  https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_topnav-->
    </head>
    <body>
        <!-- Navbar (sit on top) -->
        <div class="w3-top">
            <div class="w3-bar w3-white w3-wide w3-padding w3-card">
                <a href="index.html" class="w3-bar-item w3-button" style="font-family: Georgia, serif;">What's In My Neck of the Woods?</a>
                <!-- Float links to the right. Hide them on small screens -->
                <div class="w3-right w3-hide-small">
                    <a href="queryPage.php" class="w3-bar-item w3-button" style="font-family: Georgia, serif;">Statistics</a>
                    <!--<a href="about.html" class="w3-bar-item w3-button">Project</a> -->
                    <!--<a href="#contact" class="w3-bar-item w3-button">Contact</a> -->
                </div><!-- Float links to the right. Hide them on small screens -->
                <div class="w3-right w3-hide-small">
                    <a href="insertPage.php" class="w3-bar-item w3-button" style="font-family: Georgia, serif;">Insert a Location</a>
                    <!--<a href="about.html" class="w3-bar-item w3-button">Project</a> -->
                    <!--<a href="#contact" class="w3-bar-item w3-button">Contact</a> -->
                </div>
                <div class="w3-right w3-hide-small">
                    <a href="advQueryPage.php" class="w3-bar-item w3-button" style="font-family: Georgia, serif;">Advanced Search</a>
                    <!--<a href="about.html" class="w3-bar-item w3-button">Project</a> -->
                    <!--<a href="#contact" class="w3-bar-item w3-button">Contact</a> -->
                </div>
            </div>
        </div>
        <div class="header">
            <h1 class='instruct'>Insert Locations</h1>
            <h3 class="instruction">Add your house, favorite restaurant, school, whatever you'd like, and see how safe it is!</h3>
        </div>
        <form>
            <table class = "user_input" border="1" cellpadding="3" >
                <tr>
                    <th>Name <font color="red">*</font></th>
                    <th>Type <font color="red">*</font></th>
                    <th>Latitude <font color="red">*</font></th>
                    <th>Longitude <font color="red">*</font></th>
                    <th></th>
                </tr>
                <?php
                    if (!empty($_REQUEST["insert"])) {
                        $name = $_REQUEST["name"];
                        $type = $_REQUEST["type"];
                        $latitude = $_REQUEST["latitude"];
                        $longitude = $_REQUEST["longitude"];
                        if(!empty($name) && !empty($type) && !empty($longitude) && !empty($latitude)) {
                            echo "<tr>";
                            echo "<td>" . $name . "</td>";
                            echo "<td>" . $type . "</td>";
                            echo "<td>" . $latitude . "</td>";
                            echo "<td>" . $longitude . "</td>";
                            echo "</tr>";
                            query::insert($type, $name, $latitude, $longitude);
                        } 
                    }
                ?>
                <tr>
                    <td><input type='text' class='w3-section w3-border' required name='name' size='20'/></td>
                    <td><input type='text' class='w3-section w3-border' required name='type' size='20'/></td>
                    <td><input type='text' class='w3-section w3-border' required id='lat' name='latitude' size='20' style="font-weight: bold" readonly/></td>
                    <td><input type='text' class='w3-section w3-border' required id='long' name='longitude' size='20' style="font-weight: bold" readonly/></td>
                    <td><input type='submit' class='advanced_button' name='insert' value='Insert'/></td>
                </tr>
            </table>
        </form>
        <!-- map -->
        <div style='width:75%' id="mapid" class ="map"></div>
        <script>
            var mymap = L.map('mapid').setView([38.4496, -78.8689], 8);

            var layerGroup = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiZGV2aW5hdXN0aW4xMTIiLCJhIjoiY2p1dnlmYzR0MDZ0cDRka2lvMzIydGJ6ZyJ9.xtNhagSY6F3jzhsHvfyjKg', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox.streets',
                accessToken: 'your.mapbox.access.token'
            }).addTo(mymap);


            var marker;
            mymap.on("click", function (e) {
                if (marker) {
                    mymap.removeLayer(marker);
                }
                marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(mymap);
                document.getElementById('long').value = e.latlng.lng;
                document.getElementById('lat').value = e.latlng.lat;
            });
        </script>
    </body>
</html>

