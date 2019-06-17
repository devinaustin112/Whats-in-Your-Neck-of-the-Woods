DROP FUNCTION IF EXISTS closest_location_to_coordinates(latitude double precision, longitude double precision);

CREATE FUNCTION closest_location_to_coordinates(latitude double precision, longitude double precision)
RETURNS TABLE(location_id integer, location_type text, location_name text, latitude double precision, 
              longitude double precision, dist_mi double precision) AS $$
	
	SELECT DISTINCT location_id, location_type, location_name, l.latitude, l.longitude,
		-- get rough estimate of distance in miles (actual distance varies based on curvature of earth)
		(SELECT DIST_IN_MI($1	, $2	, l.latitude, l.longitude)) AS dist_mi
	FROM location AS l
	WHERE (SELECT DIST_IN_MI($1, $2, l.latitude, l.longitude)) < 1000 -- distance within search radius
	ORDER BY dist_mi ASC
  LIMIT 1

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION closest_location_to_coordinates(latitude double precision, longitude double precision) OWNER TO dragons;
