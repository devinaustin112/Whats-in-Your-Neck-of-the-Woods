
--Author: Maddie Brower

DROP FUNCTION IF EXISTS insert_location(name varchar, type varchar, longitude decimal, latitude decimal);

CREATE FUNCTION insert_location(name varchar, type varchar, longitude decimal, latitude decimal)
RETURNS void AS $$

    INSERT INTO location (location_type, location_name, latitude, longitude)
        VALUES ($2, $1, $4, $3);
    
$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION insert_location(name varchar, type varchar, longitude decimal, latitude decimal) OWNER TO dragons;
