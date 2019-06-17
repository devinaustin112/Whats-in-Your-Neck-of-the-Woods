DROP FUNCTION IF EXISTS closest_county_to_coordinates(latitude double precision, longitude double precision);

CREATE FUNCTION closest_county_to_coordinates(latitude double precision, longitude double precision)
RETURNS TABLE(county_id integer, station_id integer, county_name text, latitude double precision, 
              longitude double precision, dist_mi double precision) AS $$
	
	SELECT DISTINCT county_id, station_id, county_name, c.latitude, c.longitude,
		-- get rough estimate of distance in miles (actual distance varies based on curvature of earth)
		(SELECT DIST_IN_MI($1	, $2	, c.latitude, c.longitude)) AS dist_mi
	FROM county AS c
	WHERE (SELECT DIST_IN_MI($1, $2, c.latitude, c.longitude)) < 1000 -- distance within search radius
	ORDER BY dist_mi ASC
  LIMIT 1

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION closest_county_to_coordinates(latitude double precision, longitude double precision) OWNER TO dragons;
