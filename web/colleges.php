<?php
include "Database.php";
include 'query.php';
?>
<!DOCTYPE html>
<html>
  <head>
      <link rel="stylesheet" href="style.css">
      <link rel="stylesheet" href="w3.css">

  </head>

  <body>
      <div class ="chart">
          <h3 style='font-family: Georgia, serif;'>Top Ten Safest Colleges</h3>
                <table border="1" cellpadding="3">
                <tr>
                 <td colspan="3" bgcolor="Lavender">
                     <form>
                     <input type="hidden" name="query" value="Top Ten Colleges" >
                       <select name="year" onchange="this.form.submit()"> 
                           <option></option>
                       <?php
                            for ($i = 2000; $i < 2015; $i++) {
                                echo '<option name="year" value="' . $i . '">' . $i . '</option>';
                            }
                            
                         ?>
                       </select> 
                     </form>  <?php
                            if (!empty($_GET["year"])) {
                                echo "Results shown for: " . $_GET["year"];
                            } else {
                                echo "Results shown for: " . 2014;
                            }
?>
                  </td>
                </tr>
                <tr>
                   <th>College Name</th>
                   <th>Total Crimes</th>
                   <td rowspan="12">
                      <div id="piechart" style="width: 500px; height: 300px;"></div>
                   </td>
                </tr>
                <?php
                if (!empty($_GET["year"])) {
                    $year = $_GET["year"];
                } else {
                    $year = 2014;
                }
                $rs = query::topten_colleges($year);
                while ($row = pg_fetch_row($rs)) {
                    echo "<tr><td>" . $row[0] . "</td>";
                    echo "<td>" . $row[2] . "</td></tr>";
                }
                ?>
                </table>
      </div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages': ['corechart']});
            //google.charts.setOnLoadCallback(drawChartSafestCounties);
            google.charts.setOnLoadCallback(drawChartColleges);

            function drawChartColleges() {
                var ar = [
<?php
            if (!empty($_GET["year"])) {
                $year = $_GET["year"];
            } else {
                $year = 2000;
            }
            $rs = query::topten_colleges($year);
            echo '["School Name", "Dec 1st Count"],';
            for ($i = 0; $i < 9; $i++) {
                $row = pg_fetch_row($rs);
                echo '["' . $row[0];
                echo '",' . $row[2] . "],\n";
            }
            $row = pg_fetch_row($rs);
            echo '["' . $row[0];
            echo '",' . $row[2] . "]\n"
?>];
                var data = google.visualization.arrayToDataTable(ar);

                var options = {
                    chartArea: {width: '90%', height: '90%'},
                };

                var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                chart.draw(data, options);
            }
    </script>
  </body>
</html>