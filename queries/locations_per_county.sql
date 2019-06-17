DROP FUNCTION IF EXISTS locations_per_county(county_id integer);

CREATE FUNCTION locations_per_county(county_id integer)
RETURNS TABLE(location_id integer, location_name text, location_type varchar(50), lat double precision, long double precision) AS $$

SELECT location_id, location_name, location_type, lat, long FROM(SELECT 
	location_id, location_name, location_type, A.latitude AS lat, A.longitude AS long, 
	B.sqr_miles, B.county_id, B.county_name, B.longitude, B.latitude, 
	111.045 * DEGREES(ACOS(COS(RADIANS(B.latitude))
 * COS(RADIANS(A.latitude))
 * COS(RADIANS(A.longitude) - RADIANS(B.longitude))
 + SIN(RADIANS(B.latitude))
 * SIN(RADIANS(A.latitude))))
 AS distance_in_km
FROM location AS A, county AS B
WHERE county_id = $1) AS subq
WHERE distance_in_km < SQRT(subq.sqr_miles / 3.14)
ORDER BY subq.county_name, distance_in_km ASC

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION locations_per_county(county_id integer) OWNER TO dragons;


