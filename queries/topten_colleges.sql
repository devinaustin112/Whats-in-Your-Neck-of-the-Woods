-- Query to collect the top ten colleges in Virginia that have the ten
-- lowest aggregated number of crimes a given year
-- (range of 2000 - 2014) (Final query will
-- likely aggregate crime data over the last five years.)
-- Author: Courtenay Taylor

DROP FUNCTION IF EXISTS topten_colleges(year integer);

CREATE FUNCTION topten_colleges(year integer)
RETURNS TABLE(college_name text, county_name text, crime_total bigint) AS $$

  SELECT location_name AS college_name, counties.county_name, crime_count AS crime_total
	-- get every county ordered by crime rate in ASC order
	FROM get_top_N_Safest_Counties(999999999, $1, $1) AS counties
		-- get every location in each county
		JOIN locations_per_county(counties.county_id) AS locs ON 1 = 1
		JOIN county AS c ON c.county_id = counties.county_id
		JOIN crime_report AS cr ON cr.station_id = c.station_id
			AND cr.year = $1
	WHERE locs.location_type = 'college'
	ORDER BY counties.crime_count ASC
	LIMIT 10;
    

$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION topten_colleges(year integer) OWNER TO dragons;
