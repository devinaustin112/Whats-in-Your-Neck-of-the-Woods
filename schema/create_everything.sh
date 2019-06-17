# must be run from schema directory
psql -h data.cs.jmu.edu -a -d dragons -f create.sql
./copy.sh
cd ..
python3 csv_upload.py
