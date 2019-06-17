-- Purpose: Retrieves the stations with top N murder counts between year1
--	and year2 inclusive.
--  Author: Anthony 'AJ' Snarr


DROP FUNCTION IF EXISTS top_N_Murders_Over_Years(num_results integer, year1 integer, year2 integer);
CREATE FUNCTION top_N_Murders_Over_Years(num_results integer, year1 integer, year2 integer)
RETURNS TABLE(station_name text, num_murders bigint) AS $$
	SELECT s.station_name, SUM(cr.murder) as num_murders
	FROM station AS s
		JOIN crime_report AS cr
			ON s.station_id = cr.station_id
			AND cr.year BETWEEN $2 and $3
	GROUP BY cr.station_id, s.station_id
	ORDER BY SUM(cr.murder) desc, s.station_name
	LIMIT $1;
$$ LANGUAGE SQL STABLE STRICT;

ALTER FUNCTION top_N_Murders_Over_Years(num_results integer, year1 integer, year2 integer) OWNER TO dragons;
