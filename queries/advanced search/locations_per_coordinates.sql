DROP FUNCTION IF EXISTS locations_per_coordinates(latitude double precision, longitude double precision, search_radius double precision);

-- NOTE: search radius is in miles
CREATE FUNCTION locations_per_coordinates(latitude double precision, longitude double precision, search_radius double precision)
RETURNS TABLE(location_id integer, location_type varchar(100), location_name varchar(100),
			  latitude double precision, longitude double precision, dist_mi double precision) AS $$
	
	SELECT DISTINCT location_id, location_type, location_name, latitude, longitude,
		-- get rough estimate of distance in miles (actual distance varies based on curvature of earth)
		(SELECT DIST_IN_MI($1, $2, l.latitude, l.longitude)) AS dist_mi
	FROM location AS l
	WHERE (SELECT DIST_IN_MI($1, $2, l.latitude, l.longitude)) < $3 -- distance within search radius
	ORDER BY location_id;

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION locations_per_coordinates(latitude double precision, longitude double precision, search_radius double precision) OWNER TO dragons;