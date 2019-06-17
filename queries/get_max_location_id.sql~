
--Author: Maddie Brower

DROP FUNCTION IF EXISTS get_max_location_id();

CREATE FUNCTION get_max_location_id()
RETURNS TABLE(location_id integer) AS $$

    SELECT MAX(location_id)
	FROM location
    
$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION get_max_location_id() OWNER TO dragons;
