DROP FUNCTION IF EXISTS station_crime_over_years(year1 integer, year2 integer);

-- Get all crimes summed from year1 to year2, seperated by station
-- If getting all crimes, set year1 to 0 and year2 as a HUGE number
CREATE FUNCTION station_crime_over_years(year1 integer, year2 integer)
RETURNS TABLE(station_id int, station_name varchar(100),
			  violent_crime bigint, murder bigint, rape bigint,
			 robbery bigint, assault bigint, property_crime bigint,
			 theft bigint, vehicle_theft bigint) AS $$
	
	SELECT s.station_id, s.station_name, SUM(cr.violent_crime) AS violent_crime,
		SUM(cr.murder) AS murder, SUM(cr.rape) AS rape, SUM(cr.robbery) AS robbery,
		SUM(cr.assault) AS assault, SUM(cr.property_crime) AS property_crime,
		SUM(cr.theft) AS theft, SUM(cr.vehicle_theft) AS vehicle_theft
	FROM station AS s
		JOIN crime_report AS cr
			ON s.station_id = cr.station_id
			AND cr.year BETWEEN $1 and $2
	GROUP BY s.station_id

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION station_crime_over_years(year1 integer, year2 integer) OWNER TO dragons;
