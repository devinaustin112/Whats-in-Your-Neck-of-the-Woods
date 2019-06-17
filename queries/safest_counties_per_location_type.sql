---Get the top 10 safest counties in which there is that type of location 
-- Author: Maddie Brower
DROP FUNCTION IF EXISTS safest_counties_per_location_type(location_name text, year integer);

CREATE FUNCTION safest_counties_per_location_type(location_type text, year integer)
RETURNS TABLE(county_name text, latitude double precision, longitude double precision, crime_cnt bigint) AS $$

  SELECT DISTINCT counties.county_name, latitude, longitude, crime_count AS crime_total
    -- get every county ordered by crime rate in ASC order
    FROM get_top_N_Safest_Counties(999999999, $2, $2) AS counties
	    -- get every location in each county
	    JOIN locations_per_county(counties.county_id) AS locs ON locs.location_type = $1
    ORDER BY crime_total ASC
    LIMIT 10;

  
  
$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION safest_counties_per_location_type(location_name text, year integer) OWNER TO dragons;


