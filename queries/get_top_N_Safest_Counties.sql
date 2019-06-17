-- Gets the top N safest counties by total crimes, using crime between year1 and year2 inclusive.
-- All data is ordering in ascending order of crimes. If there are less than N total counties,
-- returns all counties.
--
-- Author: Anthony 'AJ' Snarr

DROP FUNCTION IF EXISTS get_top_N_Safest_Counties(num_results integer, year1 integer, year2 integer);

CREATE FUNCTION get_top_N_Safest_Counties(num_results integer, year1 integer, year2 integer)
RETURNS TABLE(county_id integer, county_name varchar, latitude double precision, longitude double precision, crime_count bigint) AS $$

    SELECT c.county_id, c.county_name, c.latitude, c.longitude, SUM(cr.violent_crime + cr.property_crime) AS crime_count
    FROM station AS s
	    JOIN crime_report AS cr ON s.station_id = cr.station_id
			AND cr.year BETWEEN $2 and $3
	    JOIN county AS c ON c.station_id = s.station_id
    GROUP BY c.county_id, c.county_name, c.latitude, c.longitude
    ORDER BY crime_count ASC
	LIMIT $1;
    
$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION get_top_N_Safest_Counties(num_results integer, year1 integer, year2 integer) OWNER TO dragons;

