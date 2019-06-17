DROP FUNCTION IF EXISTS DIST_IN_MI(latitude1 double precision, longitude1 double precision, latitude2 double precision, longitude2 double precision);

-- returns rough estimate of distance between given coordinates in miles (actual distance is based on the curvature of the earth)
CREATE FUNCTION DIST_IN_MI(latitude1 double precision, longitude1 double precision, latitude2 double precision, longitude2 double precision)
RETURNS double precision AS $$

	SELECT (SQRT(($3 - $1) * ($3 - $1) * 69 * 69 + (($4 - $2) * 69 * (COS(RADIANS($1)) + COS(RADIANS($3))) / 2)^2)) AS dist_mi

$$ LANGUAGE SQL IMMUTABLE STRICT;

ALTER FUNCTION DIST_IN_MI(latitude1 double precision, longitude1 double precision, latitude2 double precision, longitude2 double precision) OWNER TO dragons;