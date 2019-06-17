DROP TABLE IF EXISTS county;

CREATE TABLE county (
	county_id integer NOT NULL ,
	station_id integer NOT NULL ,
	county_name varchar(50) NOT NULL,
    latitude float ,
    longitude float ,
    sqr_miles float ,
	PRIMARY KEY(county_id,station_id)
);

ALTER TABLE county OWNER TO dragons;

COMMENT ON TABLE county IS 'all counties and cities in Virginia';


DROP TABLE IF EXISTS location;

CREATE TABLE location (
	location_id integer PRIMARY KEY,
	location_type varchar(50) NOT NULL,
	location_name text NOT NULL,
    latitude float ,
    longitude float 
);

ALTER TABLE location OWNER TO dragons;

COMMENT ON TABLE location IS 'locations of interest in Virginia';

DROP TABLE IF EXISTS station;

CREATE TABLE station (
	station_id integer PRIMARY KEY,
	station_name varchar(80) NOT NULL
);

ALTER TABLE station OWNER TO dragons;

COMMENT ON TABLE station IS 'all local police stations in Virginia';

DROP TABLE IF EXISTS crime_report;

CREATE TABLE crime_report (
    report_id integer PRIMARY KEY,
    station_id integer NOT NULL,
    violent_crime integer NOT NULL,
	murder integer NOT NULL,
	rape integer NOT NULL,
	robbery integer NOT NULL,
	assault integer NOT NULL,
	property_crime integer NOT NULL,
	theft integer NOT NULL,
	vehicle_theft integer NOT NULL, 
	year integer NOT NULL  
);

ALTER TABLE crime_report OWNER to dragons;

COMMENT ON TABLE crime_report IS 'all crime reports per station per year available';

