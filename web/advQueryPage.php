<?php
            include "query.php";
            include "Database.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Advanced Search</title>
        <!--        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/botstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
                      crossorigin="anonymous">-->
        <link rel="stylesheet" href="style.css">
        <link rel="icon" href="tree.png">
        <link rel="stylesheet" href="w3.css">
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

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
        <!--    responsive top nav  https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_topnav-->
    </head>   
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
        <h1 class='instruct'>Advanced Search</h1>
        <h2 class='instruct'>Find points of interest based off of crime statistics! Customize the search to your desires!</h2>
    </div>

    <?php
                //set default values for request
                $invalid_input = false;
                $rs_exist = false;
                $rs_none = false;
                //Get REQUEST for search button pressed.
                if (!empty($_REQUEST["action"])) {
                    $allProperty = false;
                    $allViolent = false;
                    $murderSelect = false;
                    $rapeSelect = false;
                    $robberySelect = false;
                    $assaultSelect = false;
                    $theftSelect = false;
                    $v_theftSelect = false;

                    $locationTypes = array();
                    $searchRad = -1;
                    $startYear = 0;
                    $endYear = 0;

                    $lat = 0;
                    $long = 0;

                    // overall selection for
                    if (!empty($_REQUEST["violent_crime"]) && $_REQUEST["violent_crime"] == "on") { //Violent crime selected
                        $allViolent = true;
                    }
                    if (!empty($_REQUEST["property_crime"]) && $_REQUEST["property_crime"] == "on") { //Property crime selected
                        $allProperty = true;
                    }
                    //Specific selection for crimes
                    if (!empty($_REQUEST["murder"]) && $_REQUEST["murder"] == "on") {
                        $murderSelect = true;
                    }
                    if (!empty($_REQUEST["rape"]) && $_REQUEST["rape"] == "on") {
                        $rapeSelect = true;
                    }
                    if (!empty($_REQUEST["robbery"]) && $_REQUEST["robbery"] == "on") {
                        $robberySelect = true;
                    }
                    if (!empty($_REQUEST["assault"]) && $_REQUEST["assault"] == "on") {
                        $assaultSelect = true;
                    }
                    if (!empty($_REQUEST["theft"]) && $_REQUEST["theft"] == "on") {
                        $theftSelect = true;
                    }
                    if (!empty($_REQUEST["vehicle_theft"]) && $_REQUEST["vehicle_theft"] == "on") {
                        $v_theftSelect = true;
                    }

                    $crimeSelect = array($murderSelect, $rapeSelect, $robberySelect, $assaultSelect,
                        $theftSelect, $v_theftSelect);
                    // Get the type of location specified, if any.
                    if (!empty($_REQUEST["location"])) {
                        array_push($locationTypes, $_REQUEST["location"]);
                    }
                    // Get the year range of data, inclusive. (Between 2000-2014)
                    if (!empty($_REQUEST["year1"])) {
                        $startYear = $_REQUEST["year1"];
                    }
                    if (!empty($_REQUEST["year2"])) {
                        $endYear = $_REQUEST["year2"];
                    }
                    // Get the number of results to return.
                    if (!empty($_REQUEST["num_results"])) {
                        $numResults = $_REQUEST["num_results"]; //Either 5, 10, 25, 50, 75, or 100
                    }
                    // Get the order to display the results in.
                    if (!empty($_REQUEST["order"])) {
                        $ordering = $_REQUEST["order"]; //Either "crime_incr" or "crime_decr"
                    }
                    // Get the search radius
                    if (!empty($_REQUEST["radius"])) {
                        $searchRad = $_REQUEST["radius"];
                    }
                    // Get the latitude
                    if (!empty($_REQUEST["latitude"])) {
                        $lat = $_REQUEST["latitude"];
                    }
                    // Get the longitude
                    if (!empty($_REQUEST["longitude"])) {
                        $long = $_REQUEST["longitude"];
                    }

                    $crimesAllowed = array($murderSelect, $rapeSelect, $robberySelect,
                        $assaultSelect, $theftSelect, $v_theftSelect);

                    $isCrimeDecr = false;
                    if ($ordering == "crime_decr") {
                        $isCrimeDecr = true;
                    }


                    list($rs_cols, $rs) = query::advanced_search($lat, $long, $searchRad, $allViolent, $allProperty, $crimesAllowed, $locationTypes, $startYear, $endYear, $isCrimeDecr, $numResults);


                    if ($rs == null) {
                        $invalid_input = true;
                    }
                    if (!empty($rs)) {
                        $rs_exist = true;
                        if (pg_num_rows($rs) == 0) {
                            $rs_none = true;
                        }
                    }
                }
    ?>      
    <form class='advanced_form' method="post">
        <!-- Map Selection -->
        <div style='width:75%;z-index: 0' id="mapid" class ="map"></div>
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
        <div style='width: 50%; margin: 0 auto;'>
            <p>
                Latitude: <input type='text' id='lat' class='w3-section w3-border' required style="font-weight: bold" name='latitude' />
                <font color="red">*</font>
                Longitude: <input type='text' id='long' class='w3-section w3-border' required style="font-weight: bold" name='longitude'/>
                <font color="red">*</font>
            </p>
            <!-- Search Radius (in miles) -->
            <p>Search radius (mi):</p>
            <p>
            <div id="slidecontainer">
                <input type="range" left="100px" min="1" max="500" value="10" id="radius" name="radius">
                <span>Value:</span>
                <span id = "f" style="font-weight: bold;color:cornflowerblue">50</span> miles
                <script>
                    var slider = document.getElementById("radius");
                    var output = document.getElementById("f");
                    output.innerHTML = slider.value;

                    slider.onchange = function () {
                        output.innerHTML = this.value;
                    }

                    slider.oninput = function () {
                        output.innerHTML = this.value;
                    }
                </script>
            </div>    
            </p>
            <!-- script for search radius -->
            <p>Please choose at least one type of crime.<font color="red">*</font></p>
            <!-- Checkboxes for Crime -->
            <p class="all">
                <input id="selectall" type="checkbox" name="all_crimes" onclick="selectAll()" /> All Crimes</p>
            <span class="indent1">
                <p><input id="violent" type="checkbox" name="violent_crime" onclick="selectViolent()" /> Violent Crimes</p>
                <span class='indent2'>
                    <p><input id="murder" type="checkbox" name="murder" /> Murder</p>
                    <p><input id="rape" type="checkbox" name="rape" /> Rape</p>
                    <p><input id="robbery" type="checkbox" name="robbery" /> Robbery</p>
                    <p><input id="assault" type="checkbox" name="assault" /> Assault</p>
                </span>
            </span>
            <span class="indent1">
                <p><input id="property" type="checkbox" name="property_crime" onclick='selectProperty()'/> Property Crimes</p>
                <span class='indent2'>
                    <p><input id="theft" type="checkbox" name="theft" /> Theft</p>
                    <p><input id="vehicle" type="checkbox" name="vehicle_theft" /> Vehicle Theft</p>
                </span>
            </span>
            <script>
                $('#selectall').change(function () {
                    if ($(this).prop('checked')) {
                        $('input').prop('checked', true);
                    } else {
                        $('input').prop('checked', false);
                    }
                });
                $('#violent').change(function () {
                    if ($(this).prop('checked')) {
                        $('#murder').prop('checked', true);
                        $('#rape').prop('checked', true);
                        $('#robbery').prop('checked', true);
                        $('#assault').prop('checked', true);
                    } else {
                        $('#murder').prop('checked', false);
                        $('#rape').prop('checked', false);
                        $('#robbery').prop('checked', false);
                        $('#assault').prop('checked', false);
                    }
                });
                $('#property').change(function () {
                    if ($(this).prop('checked')) {
                        $('#theft').prop('checked', true);
                        $('#vehicle').prop('checked', true);

                    } else {
                        $('#theft').prop('checked', false);
                        $('#vehicle').prop('checked', false);

                    }
                });

            </script>       
            <!-- location type selection(s) -->
            <p>
                Location Type: 
                <select id="location" class="w3-section w3-border" required name="location"> 
                    <option value=""></option> <!-- default option -->
                    <?php
                                $rws = query::get_all_location_types();
                                while ($row = pg_fetch_row($rws)) {
                                    echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
                                }
                    ?>
                </select>
                <font color="red">*</font>
            </p>
            <!-- year range dropdowns -->
            <p>
                Showing crimes for years between 
                <select id="year1" name="year1">
                    <option value="">any</option> <!-- default option -->
                    <?php
                                for ($i = 2000; $i < 2015; $i++) {
                                    echo '<option name="year" value="' . $i . '">' . $i . '</option>';
                                }
                    ?>
                </select>
                and
                <select id="year2" name="year2">
                    <option value="">any</option> <!-- default option -->
                    <?php
                                for ($i = 2000; $i < 2015; $i++) {
                                    echo '<option name="year" value="' . $i . '">' . $i . '</option>';
                                }
                    ?>
                </select>
            </p>
            <!-- num results dropdown -->
            <p>
                Number of Results: 
                <select id="num_results" name="num_results"> 
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25" selected="selected">25</option>
                    <option value="50">50</option>
                    <option value="75">75</option>
                    <option value="100">100</option>
                </select>
            </p>
            <!-- results ordering dropdown (less or more crime) -->
            <p>
                Results ordered by  
                <select id="order" name="order"> 
                    <option value="crime_incr">Least Crime First</option>
                    <option value="crime_decr">Most Crime First</option>
                </select>
            </p>
            <!-- Search Button -->
            <input class='advanced_button chart' style='margin-left: 25px' type='submit' name='action' value='Search'/>
        </div>
    </form>


    <!-- for checkboxes, see https://stackoverflow.com/questions/19174727/check-other-checkboxes-when-check-one-box -->

    <!-- RESULTS (in table form) -->
    <?php
                if ($rs_exist) {

                    if ($rs_none) {
                        echo '<font color="blue">No records were found with that criteria.</font>';
                    } else {
                        ?>
                        <div style='overflow-x: auto'>
                            <table  class='chart'>
                                <thead>
                                    <tr>
                                        <?php
                                        // show all but first col with id number
                                        for ($i = 1; $i < count($rs_cols); $i++) {
                                            $pretty_name = str_replace("_", " ", $rs_cols[$i]); // make into seperate words
                                            $pretty_name = ucwords($pretty_name); // capitalize first letter of each word
                                            echo '<th>' . $pretty_name . '</th>';
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = pg_fetch_row($rs)) {
                                        echo '<tr><td>';
                                        unset($row[0]); // don't show id number
                                        echo implode('</td><td>', $row);
                                        echo '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                } else if ($invalid_input) {
                    echo '<script language="javascript">';
                    echo 'alert("You must check at least one type of crime.")';
                    echo '</script>';
                }
    ?>
</html>

