-- Purpose: Retrieves the top 10 counties with the most combined violent and property crime in 
-- the years 2005 to 2010
-- Author: Maggie Duke

DROP FUNCTION IF EXISTS top_Ten_Crime_Counties(year integer);

CREATE FUNCTION top_Ten_Crime_Counties(year integer)
RETURNS TABLE(county_name text, crime_total integer) as $$

    SELECT co.county_name, SUM(c.violent_crime + c.property_crime)
    FROM station AS s
	    JOIN crime_report AS c ON s.station_id = c.station_id
	    JOIN county AS co ON co.station_id = s.station_id
    WHERE c.year = $1
    GROUP BY co.county_name
    ORDER BY SUM(c.violent_crime + c.property_crime) DESC
    LIMIT 10;


$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION top_Ten_Crime_Counties(year integer) OWNER TO dragons;
