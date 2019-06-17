ALTER TABLE county ADD PRIMARY KEY (county_id, station_id);

ALTER TABLE city ADD PRIMARY KEY (city_id);
ALTER TABLE city ADD FOREIGN KEY (county_id) REFERENCES county;
ALTER TABLE city ADD FOREIGN KEY (station_id) REFERENCES station;

ALTER TABLE location ADD PRIMARY KEY (location_id);
ALTER TABLE location ADD FOREIGN KEY (city_id) REFERENCES city;
ALTER TABLE location ADD FOREIGN KEY (county_id) REFERENCES county;

ALTER TABLE station ADD PRIMARY KEY (station_id);

ALTER TABLE crime_report ADD PRIMARY KEY (report_id);
ALTER TABLE crime_report ADD FOREIGN KEY (station_id) REFERENCES station;
