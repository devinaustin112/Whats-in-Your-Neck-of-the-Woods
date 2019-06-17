DROP FUNCTION IF EXISTS counties_per_location(location_id integer);

CREATE FUNCTION counties_per_location(location_id integer)
RETURNS TABLE(county_id integer, station_id integer, county_name varchar(100), dist_mi double precision) AS $$
	
	SELECT DISTINCT county_id, station_id, county_name,
		(SELECT DIST_IN_MI(c.latitude, c.longitude, l.latitude, l.longitude)) as dist_mi
	FROM county AS c
		JOIN location AS l ON l.location_id = $1
	-- get rough estimate of distance in miles (actual distance varies based on curvature of earth)
	WHERE (SELECT DIST_IN_MI(c.latitude, c.longitude, l.latitude, l.longitude))
		< SQRT(c.sqr_miles / 3.14159) -- distance less than radius of county
	ORDER BY county_name;

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION counties_per_location(location_id integer) OWNER TO dragons;