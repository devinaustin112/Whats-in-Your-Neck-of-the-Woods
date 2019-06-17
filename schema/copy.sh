
#!/bin/sh

#echo COPY county FROM csv
#psql -c "\copy county FROM ../csvData/county.csv WITH CSV HEADER" -h data.cs.jmu.edu -d dragons

echo COPY location FROM csv
psql -c "\copy location FROM ../csvData/location_new.csv WITH CSV HEADER" -h data.cs.jmu.edu -d dragons



#echo COPY location FROM csv
#psql -c "\copy location FROM ../csvData/location.csv WITH CSV HEADER" -h data.cs.jmu.edu -d dragons

#echo COPY station FROM csv
#psql -c "\copy station FROM station.csv WITH CSV HEADER" -h data.cs.jmu.edu -d dragons
