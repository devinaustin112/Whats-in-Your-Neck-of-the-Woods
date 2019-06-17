<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of query
 *
 * @author dukema
 */
class query {    
    
    /**
     * Runs an "advanced search" query with the given parameters.
     * 
     * @param double lat center latitude
     * @param double long center longitude
     * @param double search_rad search radius
     * @param bool $is_all_violent_crime whether or not all violent crime is enabled
     * @param bool $is_all_property_crime whether or not all property crime is enabled
     * @param Array[bool] $list_crimes_allowed array of boolean values saying what
     *      crimes were enabled (does not matter if previous two booleans are
     *      both true). Looks like:
     *          [murder, rape, robbery, assault, theft, vehicle_theft]
     * @param Array[str] list_loc_types array of location types
     * @param double year_min min year in range. If 0 there is no min
     * @param double year_max max year in range. If 0 there is no max
     * @param bool $is_crimes_decr if true, results are listed in order of
     *      decreasing crime count, if false, results are listed in order of
     *      increasing crime count
     * @param int $num_res number of results to return. If 0, there is no limit
     * 
     * @return an array, with a list of returned collumn names,
     *         and results from query, or NULL if input was invalid
     */
    public static function advanced_search($lat, $long, $search_rad,
            $is_all_violent_crime, $is_all_property_crime, $list_crimes_allowed,
            $list_loc_types, $year_min, $year_max, $is_crimes_decr, $num_res) {
        
        // validate input
        if ($lat == null || $long == null)
            return array(null, null);
        if (empty($list_loc_types))
            return array(null, null); // some location types must be given
        if (empty($list_crimes_allowed) || count($list_crimes_allowed) != 6)
            return array(null, null);
        if ($search_rad <= 0)
            return array(null, null);
        if (false == ($is_all_violent_crime || $is_all_property_crime
                || in_array(false, $list_crimes_allowed)))
            return array(null, null); // at least one crime must be enabled
        if ($num_res < 0)
            return array(null, null); // 0 results means no limit, but <0 is undefined
        if ($year_max < 0 || $year_min < 0 || $year_max < $year_min) 
            return array(null, null);
        
        // make connection
        $con = Database::open();
        
        // format input
        $ymin = $year_min;
        $ymax = $year_max;
        if ($year_max == 0)
            $ymax = 2147483647;
        if ($year_min == 0)
            $ymin = 0;
        
        // form sql query
        $query_format = "SELECT %s FROM crime_in_search_radius(%f, %f, %f, %d, %d) WHERE %s ORDER BY %s %s;";
        
        $default_select = "location_id, location_name, location_type, county_name";
        $crime_select = query::get_crime_restrictors($is_all_violent_crime, $is_all_property_crime, $list_crimes_allowed);
        
        $actual_select = $default_select;
        if ($crime_select != "") {
            $actual_select = $default_select . ", " . $crime_select;
        }
        
        $actual_where = query::get_location_restrictors($list_loc_types);
        
        $order = query::get_advanced_ordering(explode(", ", $crime_select),
                $is_crimes_decr);
        
        $limit_by = "";
        if ($num_res > 0) {
            $limit_by = "LIMIT " . $num_res;
        }
        
        $sql = sprintf($query_format, $actual_select, $lat, $long,
                $search_rad, $ymin, $ymax, $actual_where, $order, $limit_by);
        
        $ret_cols = explode(", ", $actual_select);
        
        // run query and close connection
        pg_prepare($con, "advanced_search", $sql);
        $rws = pg_execute($con, "advanced_search", array())
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return array($ret_cols, $rws);
    }
    
    /**
     * Returns a piece of a psql SELECT clause for picking out crime types.
     * 
     * @param bool $is_all_violent_crime whether or not all violent crime is enabled
     * @param bool $is_all_property_crime whether or not all property crime is enabled
     * @param Array[bool] $list_crimes_allowed array of boolean values saying what
     *      crimes were enabled (does not matter if previous two booleans are
     *      both true). Looks like:
     *          [murder, rape, robbery, assault, theft, vehicle_theft]
     */
    private static function get_crime_restrictors($is_all_violent_crime, $is_all_property_crime, $list_crimes_allowed) {
        
        $strs = array("all_crime", "violent_crime", "property_crime", "murder", "rape",
            "robbery", "assault", "theft", "vehicle_theft");
        $enabled = array(false, false, false, false, false, false, false, false, false);
        
        if ($is_all_property_crime == true && $is_all_violent_crime == true) {
            $enabled[0] = true;
        }
        
        // enable types of violent crime
        if ($is_all_violent_crime == true) {
            $enabled[1] = true;
            for ($i = 3; $i <= 6; $i++) {
                $enabled[$i] = true;
            }
        } else {
            for ($i = 3; $i <= 6; $i++) {
                $enabled[$i] = $list_crimes_allowed[$i-3];
            }
        }
        
        // enable types of property crime
        if ($is_all_property_crime == true) {
            $enabled[2] = true;
            for ($i = 7; $i <= 8; $i++) {
                $enabled[$i] = true;
            }
        } else {
            for ($i = 7; $i <= 8; $i++) {
                $enabled[$i] = $list_crimes_allowed[$i-3];
            }
        }
        
        // add enabled strings to a stack
        $to_select = array();
        for ($i = 0; $i < 9; $i++) {
            if ($enabled[$i]) {
                array_push($to_select, $strs[$i]);
            }
        }
        
        // return all values separated by commas
        return implode(", ", $to_select);
    }
    
    /**
     * Returns a piece of a psql WHERE clause for picking specific location
     * types.
     * 
     * @param Array[str] $list_loc_types array of location types to include
     */
    private static function  get_location_restrictors($list_loc_types) {
        $where_list = array();
        foreach ($list_loc_types as $loc_type) {
            array_push($where_list, "location_type = '" . $loc_type . "'");
        }
        
        return "(" . implode(" || ", $where_list) . ")";
    }
    
    /**
     * Returns text to be put into a psql ORDER BY clause. Will look like
     * all crime summed up, increasing or decreasing
     * 
     * @param array[str] $list_crime_restrictors array of column names for
     *                                           crimes
     * @param bool $is_crime_desc if true, ordered by decreasing crime count,
     *                            if false, ordered by increasing crime count
     */
    private static function get_advanced_ordering($list_crime_restrictors, $is_crime_desc) {
        $order_type = 'ASC';
        if ($is_crime_desc) {
            $order_type = 'DESC';
        }
        
        $order_fmt = '(%s) %s';
        return sprintf($order_fmt, implode(" + ", $list_crime_restrictors),
                $order_type);
    }
    
    public static function get_stations() {
        $con = Database::open();
        $sql = "SELECT * FROM  get_stations()";
        pg_prepare($con, "station", $sql);
        $rws = pg_execute($con, "station", array())
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    public static function get_locations_per_station($stationnum, $lim) {
        $con = Database::open();
        $sql = "SELECT * FROM get_locations_per_station($1, $2)";
        pg_prepare($con, "locations_station", $sql);
        $rws = pg_execute($con, "locations_station", array($stationnum, $lim))
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    public static function safest_counties_per_location_type($locationname, $year) {
        $con = Database::open();
        $sql = "SELECT * FROM safest_counties_per_location_type($1,"
                . " $2)";
        pg_prepare($con, "safest_counties", $sql);
        $rws = pg_execute($con, "safest_counties", array($locationname, $year))
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    public static function top_n_murders_over_years($lim, $year1, $year2){
        $con = Database::open();
        $sql = "SELECT * FROM top_n_murders_over_years($1, $2, $3)";
        pg_prepare($con, "top_murders", $sql);
        $rws = pg_execute($con, "top_murders", array($lim, $year1, $year2))
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    public static function topten_colleges($year) {
        $con = Database::open();
        $sql = "SELECT * FROM topten_colleges($1)";
        pg_prepare($con, "top_colleges", $sql);
        $rws = pg_execute($con, "top_colleges", array($year))
                or die("query failed: ". pg_last_error());
        pg_close($con);
        $json = json_encode($rws);
        echo $json;
        return $rws;
    }
    
    public static function get_all_location_types() {
        $con = Database::open();
        $sql = "SELECT distinct location_type FROM location order by location_type";
        pg_prepare($con, "location_types", $sql);
        $rws = pg_execute($con, "location_types", array()) 
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    public static function top_ten_crime_counties($year) {
        $con = Database::open();
        $sql = "SELECT * FROM top_ten_crime_counties($1)";
        pg_prepare($con, "top_crimes", $sql);
        $rws = pg_execute($con, "top_crimes", array($year))
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    public static function insert($type, $name, $latitude, $longitude){
        $con = Database::open();
        $sql = "SELECT max(location_id) FROM location;";
        pg_prepare($con, "max_id", $sql);
        $rws = pg_execute($con, "max_id", array())
                or die("query failed: ". pg_last_error());
        $row = pg_fetch_row($rws);
        $max = intval($row[0]) + 1;
        $insert = "INSERT INTO location VALUES ($max, $1, $2, $3, $4)";
        pg_prepare($con, "insert", $insert);
        pg_execute($con, "insert", array($type, $name, doubleval($latitude), 
            doubleval($longitude)))
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
        public static function locations_for_location_type($type) {
        $con = Database::open();
        $sql = "SELECT * FROM locations_for_location_type($1)";
        pg_prepare($con, "locations_for_location_type", $sql);
        $rws = pg_execute($con, "locations_for_location_type", array($type))
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    
    public static function get_counties() {
        $con = Database::open();
        $sql = "SELECT DISTINCT county_name, county_id FROM county ORDER BY county_name";
        pg_prepare($con, "county", $sql);
        $rws = pg_execute($con, "county", array())
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    public static function locations_per_county($countyname) {
        $con = Database::open();
        $sql1 = "SELECT DISTINCT county_id FROM county WHERE county_name LIKE '%$countyname%'";
        pg_prepare($con, "county", $sql1);
        $rws1 = pg_execute($con, "county", array())
                or die("query failed: ". pg_last_error());
        $row = pg_fetch_row($rws1);
        $countyid = $row[0];
        $sql = "SELECT * FROM locations_per_county($1)";
        pg_prepare($con, "loc", $sql);
        $rws = pg_execute($con, "loc", array($countyid))
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return $rws;
    }
    
    public static function get_county_lat_long($countyname) {
        $con = Database::open();
        $sql = "SELECT DISTINCT latitude, longitude FROM county WHERE county_name LIKE '%$countyname%'";
        pg_prepare($con, "county", $sql);
        $rws = pg_execute($con, "county", array())
                or die("query failed: ". pg_last_error());
        pg_close($con);
        return pg_fetch_row($rws);
    }
}
