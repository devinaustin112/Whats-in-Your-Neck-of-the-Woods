DROP FUNCTION IF EXISTS single_county_per_location(location_id integer);

CREATE FUNCTION single_county_per_location(location_id integer)
RETURNS TABLE(county_id integer, station_id integer, county_name varchar(100), dist_mi double precision) AS $$
	
	SELECT county_id, station_id, county_name,
		(SELECT DIST_IN_MI(c.latitude, c.longitude, l.latitude, l.longitude)) as dist_mi
	FROM county AS c
		JOIN location AS l ON l.location_id = $1
	WHERE (SELECT DIST_IN_MI(c.latitude, c.longitude, l.latitude, l.longitude))
		< SQRT(c.sqr_miles / 3.14159) -- distance less than radius of county
	
	-- many locations may be linked to more than one county, so pick whichever one
	-- has a smaller sqr miles (an therefore might be a city within a county)
	ORDER BY c.sqr_miles ASC
	LIMIT 1

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION single_county_per_location(location_id integer) OWNER TO dragons;