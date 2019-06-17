DROP FUNCTION IF EXISTS crime_in_search_radius(latitude double precision, longitude double precision,
			radius double precision, year_min integer, year_max integer);

-- Gets all crime totals for all locations given a search radius and a span of years.
-- Each location is treated as if it is in one "county." Where small cities and the like
-- are preffered over bigger areas.

-- If getting all crimes, set year1 to 0 and year2 as a HUGE number
CREATE FUNCTION crime_in_search_radius(latitude double precision, longitude double precision,
			radius double precision, year_min integer, year_max integer)
RETURNS TABLE(location_id integer, location_type varchar(100),
			  location_name varchar(100), latitude double precision,
			  longitude double precision, dist_mi double precision,
			  county_id integer, county_name varchar(100),
			  all_crime NUMERIC(38,0), violent_crime NUMERIC(38,0), murder NUMERIC(38,0),
			  rape NUMERIC(38,0), robbery NUMERIC(38,0), assault NUMERIC(38,0),
			  property_crime NUMERIC(38,0), theft NUMERIC(38,0),
			  vehicle_theft NUMERIC(38,0)) AS $$

	-- sum over crimes for individual counties that have multiple stations
	SELECT l.location_id, l.location_type, l.location_name, l.latitude, l.longitude,
			l.dist_mi, c.county_id, c.county_name,
			SUM(cr.violent_crime + cr.property_crime) AS all_crime,
			SUM(cr.violent_crime) AS violent_crime, SUM(cr.murder) AS murder,
			SUM(cr.rape) AS rape, SUM(cr.robbery) AS robbery, SUM(cr.assault) AS assault,
			SUM(cr.property_crime) AS property_crime, SUM(cr.theft) AS theft,
			SUM(cr.vehicle_theft) AS vehicle_theft
	FROM locations_per_coordinates($1, $2, $3) AS l
		JOIN single_county_per_location(l.location_id) AS c ON 1 = 1
		JOIN station_crime_over_years($4, $5) AS cr ON cr.station_id = c.station_id
	GROUP BY l.location_id, l.location_type, l.location_name, l.latitude, l.longitude,
		l.dist_mi, c.county_id, c.county_name

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION crime_in_search_radius(latitude double precision, longitude double precision,
			radius double precision, year_min integer, year_max integer) OWNER TO dragons;