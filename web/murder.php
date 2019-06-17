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
      <div class = "chart">
        <h3 style='font-family: Georgia, serif;'>Station Murder Counts</h3>
        <form>
            <table border="1" cellpadding="3">
                    <tr>
                    <td colspan="3" bgcolor="Lavender">
                        <form>
                          <input type="hidden" name="query" value="Station Murder Count" >
                          <select name="murder_year" onchange="this.form.submit()"> 
                            <?php for ($i = 2000; $i < 2015; $i++) {
                                echo '<option name="year" value="' . $i . '">' . $i . '</option>';
                            }?>
                            
                            
                          </select>
                        </form> 

                            <?php if (!empty($_GET["murder_year"])) {
                                echo "Results shown for: " . $_GET["murder_year"];
                            }?>
                            
                    </td>
                </tr>
                <tr>
                    <th>Station</th>
                    <th>Murder Count</th>
                    <td rowspan="12">
                        <div id="chart_div" style="width: 500px; height: 300px;"></div>
                    </td>
                </tr> 
                            <?php if (!empty($_GET["murder_year"])) {
                                $year = $_GET["murder_year"];
                                $end_year = 2014;
                            } else {
                                $year = 2000;
                                $end_year = 2000;
                            }
                            $rs = query::top_n_murders_over_years(10, $year, $end_year);
                            while ($row = pg_fetch_row($rs)) {
                                echo "<tr><td>" . $row[0] . "</td>";
                                echo "<td>" . $row[1] . "</td></tr>";
                            }?>

                           
                
            </table> 
            </div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages': ['corechart']});
            //google.charts.setOnLoadCallback(drawChartSafestCounties);
            google.charts.setOnLoadCallback(drawMurderChart);

            function drawMurderChart() {
                var ar = [
<?php
            if (!empty($_GET["murder_year"])) {
                $murder_year = $_GET["murder_year"];
            } else {
                $murder_year = 2014;
            }
            $rs = query::top_n_murders_over_years(10, $murder_year, $murder_year);
            echo '["Station", "Murder Count"],';
            for ($i = 0; $i < 9; $i++) {
                $row = pg_fetch_row($rs);
                echo '["' . $row[0];
                echo '",' . $row[1] . "],\n";
            }
            $row = pg_fetch_row($rs);
            echo '["' . $row[0];
            echo '",' . $row[1] . "]\n"
?>];
                var data = google.visualization.arrayToDataTable(ar);

                var options = {
                    chartArea: {width: '90%', height: '90%'},
                };

                var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

                chart.draw(data, options);
            }
    </script>
  </body>
</html>