--Returns all of the locations given a specific station.
--Author: Devin Dyer

DROP FUNCTION IF EXISTS get_Locations_Per_Station(station_id integer, lim integer);

CREATE FUNCTION get_Locations_Per_Station(station_id integer, lim integer)
RETURNS TABLE(location_name text, location_type text) AS $$

	SELECT DISTINCT locs.location_name, locs.location_type
	-- get every county ordered by crime rate in ASC order
    FROM get_top_N_Safest_Counties(999999999, 2000, 2014) AS counties
	    -- get every location in each county
	    JOIN locations_per_county(counties.county_id) AS locs ON 1 = 1
		JOIN county AS c ON c.county_id = counties.county_id
    WHERE c.station_id = $1
   	LIMIT $2;
    
$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION get_Locations_Per_Station(station_id integer, lim integer) OWNER TO dragons;
