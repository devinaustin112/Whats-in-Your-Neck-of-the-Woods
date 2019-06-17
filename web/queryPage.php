<?php
            include "Database.php";
            include 'query.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Statistics</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
              crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="icon" href="tree.png">
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
            <h3 class='instruction'>Select from the following to see a visualization of the data</h3>
        </div>

        <form class='instruct'>
            <div class="dropdown">
                <button class="btn btn-secondary btn-block dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Statistics
                </button>
                <div class="dropdown-menu" aria-labelledby="Statistics">
                    <input class="dropdown-item" type="submit" name="query" value="Ten Safest Colleges" >
                    <input class="dropdown-item" type="submit" name="query" value="Safest Counties With Your Favorite Location">
                    <input class="dropdown-item" type="submit" name="query" value="Murder Rates Per Year">
                    <input class="dropdown-item" type="submit" name="query" value="Locations In Each County">
                    <input class="dropdown-item" type="submit" name="query" value="Tracked Police Stations">
                    <input class="dropdown-item" type="submit" name="query" value="All Tracked Locations By Type">
                </div>
            </div>
        </form>

        <?php
                    if (!empty($_REQUEST["query"])) {?>
                        <!-- top ten colleges-->
                    <?php if ($_REQUEST["query"] == ("Ten Safest Colleges")) {?>
                        
                        <div id="topBar"  > </div>
                            <div style="height:700px" id ="content"> </div>
                            <script>
                                //document.onload 
                                document.getElementById("content").innerHTML = '<object style="height:100%; width: 100%" type="text/php" data="colleges.php" ></object>';

                            </script>
                            
                            <!-- top ten crime counties-->
                        <?php } elseif ($_REQUEST["query"] == ("Safest Counties With Your Favorite Location")) {
                            ?>
                            <div id="topBar"  > </div>
                            <div class="chart" style="height:800px; width: 800px" id ="content"> </div>
                            <script>
                                //document.onload 
                                document.getElementById("content").innerHTML = '<object style="height:100%; width: 100%" type="text/php" data="safestCountiesPerLocation.php" ></object>';

                            </script>
                            
                            <!-- top n murders over years-->

                            <?php } elseif ($_REQUEST["query"] == ("Tracked Police Stations")) {
                            ?>
                            <div id="topBar"  > </div>
                            <div class="chart" style="height:700px" id ="content"> </div>
                            <script>
                                //document.onload 
                                document.getElementById("content").innerHTML = '<object style="height:100%; width: 100%" type="text/php" data="stations.php" ></object>';

                            </script>
                            
                            <!-- top n murders over years-->

                            <?php } elseif ($_REQUEST["query"] == ("Murder Rates Per Year")) {?>
                            
                            <div id="topBar"  > </div>
                            <div style="height:700px" id ="content"> </div>
                            <script>
                                //document.onload 
                                document.getElementById("content").innerHTML = '<object style="height:100%; width: 100%" type="text/php" data="murder.php" ></object>';

                            </script>
                             <!-- get locations per station-->
                        <?php } elseif ($_REQUEST["query"] == ("All Tracked Locations By Type")) {?>
                            
                            <div id="topBar"  > </div>
                            <div style="height:700px" id ="content"> </div>
                            <script>
                                //document.onload 
                                document.getElementById("content").innerHTML = '<object style="height:100%; width: 100%" type="text/php" data="locationsForLocationType.php" ></object>';

                            </script>
                             <!-- get locations per county-->
                        <?php
                        } elseif ($_REQUEST["query"] == ("Locations In Each County")) {?>
                            <div id="topBar"  > </div>
                            <div style="height:800px" id ="content"> </div>
                            <script>
                                //document.onload 
                                document.getElementById("content").innerHTML = '<object style="height:100%; width: 100%" type="text/php" data="locationsPerCounty.php" ></object>';

                            </script>
                            <?php
                        }
                    }
        ?>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>

    </body>
</html>
