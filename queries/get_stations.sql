DROP FUNCTION IF EXISTS get_stations();

CREATE FUNCTION get_stations()
RETURNS TABLE(station_name varchar(80), latitude double precision, longitude double precision) AS $$
			  
SELECT station_name, latitude, longitude FROM station
	JOIN county ON county.station_id = station.station_id
	
$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION get_stations() OWNER TO dragons;
