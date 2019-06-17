### What's In My Neck of the Woods?

Hello, world! We are a team of five undergraduate Computer Science students from James Madison University who have collaborated to bring you this web site for crime data for a majority of counties in Virginia.

## Our Mission:

We are creating a database that will help users learn about crime statistics of locations of interest. This project was conceived and executed from August 2018 - May 2019 as part of the course Database Design and Applications (CS 474). Our data comes straight from the counties' police departments and dates between 2000-2014. Over the course of developing this project, we have expanded our knowledge of Database Management System (DBMS) development and have used tools such as Git, NetBeans IDE, HTML/CSS, PHP, and Python.
        
Our vision for this project is to provide information to allow users to determine the relative safety of counties, cities, and also locations of interest such as universities, parks, and local school systems. We hope that by providing this information, users will be able to make educated decisions about where to travel or live permanently. In addition to personal informative purposes, our project also serves as an access point to aggregations of crime data in Virginia for research purposes. We found in our preliminary research that many sets of crime data in Virginia are available but dispersed among many sources. We hope that by compiling and analyzing those sets that future research in this area can be made simpler.

## What is in our DB:

Our database currently has information about various cities and counties. Each county and/or city will have at least one station that will report on a type of crime. The crimes that we are collecting statistics on are: violent_crime, murder, rape, robbery, assualt, property_crime, theft, and vehicle_theft. We also have the locations of certain places of interest in various cities and counties such as parks, colleges, and local school systems. 

## Data Sources

Here is a list of websites where we sourced all of the data for this project with the general description of each data set.

Public Places in Virginia Beach, VA
http://gis-vbgov.opendata.arcgis.com/datasets/4ee34e8a8aea431ea329743a71c2fd61_5?uiTab=table

Public Parks in Albemarle County, VA
http://data-uvalibrary.opendata.arcgis.com/datasets/9ff2102a5c30488c8193eb69c2ccf7ab_0

Latitudes and Longitudes, embedded map of locations, information on locations of interest.
https://www.openstreetmap.org/copyright
https://www.openstreetmap.org/#map=4/38.01/-95.84
OpenStreetMap is open data licensed under the Open Data Commons Open Database License (ODbL) by the OpenStreetMap Foundation (OSMF). The location data we used for our project is attributed to OpenStreetMap and its contributors.

## How to Install

Note: Since out project is a work-in-progress, these installation instructions are out of data in terms of the structure of our project. Please be patient as we update this documentation to reflect current changes. Thank you!

In order to install follow the instructions:

1. Download the locations.csv, station.csv, and county.csv
3. Run create.sql to create tables with psql -h [your data server] -U [your username] -a -f create.sql
4. Run copy.sh on data.cs.jmu.edu to copy data with ./copy.sh
5. Run alter.sql to add the correct key constraints
6. Run stats.sql to count rows and analyze the table with psql -h [your data server] -U [your username] -a -f stats.sql
