
from abc import ABC, abstractmethod
import csv
import logging
import sys

# Docs at http://initd.org/psycopg/docs/
# load via this command: 'python3 -m pip install psycopg2'
import psycopg2 as pg

import cred_info as creds

# set up logger
logging.basicConfig(stream=sys.stdout, level=logging.DEBUG)
log = logging.getLogger(__name__)


def main():
    # connect
    db = PSQL()

    # start importing csv files for crime_report
    LocalVACrimeMapping(db, '2014')
    LocalVACrimeMapping(db, '2013')
    LocalVACrimeMapping(db, '2012')
    LocalVACrimeMapping(db, '2011')
    LocalVACrimeMapping(db, '2010')
    LocalVACrimeMapping(db, '2009')
    LocalVACrimeMapping(db, '2008')
    LocalVACrimeMapping(db, '2007')
    LocalVACrimeMapping(db, '2006')
    LocalVACrimeMapping(db, '2005')
    LocalVACrimeMapping(db, '2004')
    LocalVACrimeMapping(db, '2003')
    LocalVACrimeMapping(db, '2002')
    LocalVACrimeMapping(db, '2001')
    LocalVACrimeMapping(db, '2000')
    # ...

    # start importing csv files for location
    """CollegeMapping(db)

    # Arlington locations
    ArlingtonParkMapping(db)
    ArlingtonChildDevelopmentCenterMapping(db)
    ArlingtonLibraries(db)
    ArlingtonNatureCenters(db)
    ArlingtonSchools(db)

    # Virginia Beach locations
    VBElementarySchools(db)
    VBHighSchools(db)
    VBGolfCourses(db)
    VBLibraries(db)
    VBMiddleSchools(db)
    VBMuseums(db)
    VBParks(db)
    VBRecCenters(db)"""

    # Counties
    VACounties(db)

    # close connection
    db.close()


class PSQL:
    """ Represents a db connection
    """

    INITIAL_UID = 1  # first uid assinged in a table

    def __init__(self):
        self.connect()  # creates self.con
        self.cursor = self.con.cursor()

    def connect(self):
        log.info(f'connecting to db at {creds.HOSTNAME}...')
        # con = pg.connect(creds.USERNAME, host=creds.HOSTNAME, port=creds.PORT,
        #                  database=creds.DATABASE, password=creds.PASSWORD)
        con = pg.connect(user=creds.USERNAME, host=creds.HOSTNAME,
                         port=creds.PORT, dbname=creds.DATABASE, password=creds.PASSWORD)
        log.info(f'connected to db at {creds.HOSTNAME}')
        self.con = con

    def close(self):
        self.con.close()

    def get_highest_uid(self, tablename: str, columnname: str):
        """ Retrieves the highest numerical id in the given collumn. Useful for
            adding new ids onto a table.
        """
        res = self.select(f'SELECT {columnname} FROM {tablename}')

        if res == None:
            raise Exception(
                'Highest UID operation failed: invalid select statement')
        elif len(res) == 0:
            return PSQL.INITIAL_UID - 1  # default value

        # res is a tuple of lists containing single values
        sort = [r[0] for r in res]
        sort.sort(reverse=True)  # descending order

        return sort[0]  # return highest value

    def insert(self, tablename: str, row: list):
        """ For running quick inserts.
        """
        values = '(' + ', '.join("'" + str(r) + "'" for r in row) + ')'
        query = f'INSERT INTO {tablename} VALUES{values}'
        # print(query)
        try:
            self.cursor.execute(query)
            pass
        except Exception as e:
            eStr = f'Error executing insert \'{query}\': {str(e)}'
            log.error(eStr)
        finally:
            self.con.commit()

    def select(self, query):
        """ For running quick select statements
        """
        ret = None
        try:
            self.cursor.execute(query)
            ret = self.cursor.fetchall()
        except Exception as e:
            eStr = f'Error executing select \'{query}\': {str(e)}'
            log.error(eStr)
        self.con.commit()
        return ret

##### Igore this it is an abstract class ##################


class CsvMapping(ABC):
    """ Maps rows in a csv file to rows in a table
            NOTE: this is an abstract class
        For cases where multiple csv's are needed at once, use add_exta_csvs().
    """

    def __init__(self, db: PSQL, csvFilename: str, tablenames: list, csvLinesToSkip=1):
        """
        CSV lines to skip is for skiping headers and non-data in a file. By
        default it is just one line.
        """
        self.db = db

        infile = open(csvFilename)
        for _ in range(csvLinesToSkip):
            infile.readline()  # header
        self.csvData = csv.reader(infile)

        self.csvDataList = [self.csvData]

        # start moving stuff over
        print()  # newln in stdout
        log.info(
            f'Uploading to tables {tablenames} from csv at \'{csvFilename}\'...')
        self.copy_data()
        log.info(f'Finished uploading from csv (hopefully)')

    def add_extra_csv(self, csvFilename: list, csvLinesToSkip=1):
        """ Appends to self.csvDataList, where the first index is the original csvfile

            csvLinesToSkip: list[int] - lines to skip
        """
        infile = open(csvFilename)
        for _ in range(csvLinesToSkip):
            infile.readline()  # header
        self.csvDataList.append(csv.reader(infile))

    @abstractmethod
    def copy_data(self):
        """ This is called automatically in children
        """
        pass

########## Implementations of csv mappers ##############


class LocalVACrimeMapping(CsvMapping):
    """ This class inherits from abstract CsvMapping
    """

    def __init__(self, db: PSQL, year: str):
        """
        Valid years are 2000-2014
        """
        self.year = year
        self.lastuid = db.get_highest_uid('crime_report', 'report_id')
        self.laststationuid = db.get_highest_uid('station', 'station_id')

        csvLinesToSkip = 9  # there are extra lines with info at the top of these files

        super().__init__(db, f'./csvData/va/local/VALocalCrime{year}.csv', [
            'crime_report'], csvLinesToSkip=csvLinesToSkip)

    def copy_data(self):
        # csv rows look like:
        #   [0] Agency name, [1] State, [2] Months, [3] Population, [4] Violent crime total,
        #   [5] Murder and nonnegligent Manslaughter, [6] Legacy rape /1, [7] Revised rape /2,
        #   [8] Robbery, [9] Aggravated assault, [10] Property crime total, [11] Burglary,
        #   [12] Larceny-theft, [13] Motor vehicle theft, [14] Violent Crime rate,
        #   [15] Murder and nonnegligent manslaughter rate, [16] Legacy rape rate /1,
        #   [17] Revised rape rate /2, [18] Robbery rate, [19] Aggravated assault rate,
        #   [20] Property crime rate, [21] Burglary rate, [22] Larceny-theft rate,
        #   [23] Motor vehicle theft rate,
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make sure
            if len(niceRow) < 24:
                continue

            has_station = False

            # get the station id for the current row if it exists
            station_id = None
            try:
                sql = f'SELECT station_id FROM station WHERE station_name = \'{niceRow[0]}\''
                station_id = self.db.select(sql)
                if station_id:
                    station_id = station_id[0][0]
                    has_station = True
                else:
                    self.laststationuid += 1  # increment id for next station
                    station_id = self.laststationuid
            except IndexError:
                # when niceRow[0] fails
                continue

            # make tha row (example)
            self.lastuid += 1  # increment id

            station_row = [
                station_id,
                niceRow[0],  # station name
            ]

            crime_report_row = [
                self.lastuid,  # report id
                station_id,
                niceRow[4],  # violent crime
                niceRow[5],  # murder
                niceRow[7] + niceRow[6],  # rape
                niceRow[8],  # robbery
                niceRow[9],  # assault
                niceRow[10],  # property_crime
                niceRow[11],  # burglary
                niceRow[13],  # vehicle_theft
                self.year,   # year?
            ]

            # make a relation (example)
            crime_station_row = [
                self.lastuid,  # report id
                station_id  # station id
            ]

            # insert into db
            if not has_station:
                self.db.insert('station', station_row)
            self.db.insert('crime_report', crime_report_row)

            #self.db.insert('report_station', crime_station_row)


class CollegeMapping(CsvMapping):
    """ This class inherits from abstract CsvMapping
    """

    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(db, f'./csvData/college.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   UNITID[0],INSTNM[1],ADDR[2],CITY[3],STABBR[4],ZIP[5]
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            sql = f'SELECT county_id FROM county WHERE county_name LIKE \'%{niceRow[3]}%\''
            county_id = self.db.select(sql)

            if county_id:
                county_id = county_id[0][0]
            else:
                county_id = 0

            location_row = [
                self.lastuid,
                county_id,
                'college',
                niceRow[1].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class ArlingtonParkMapping(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/Arling_Park_Polygons.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                110,
                'park',
                niceRow[2].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class ArlingtonChildDevelopmentCenterMapping(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/Arlington_Child Development Centers.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                110,
                'child development center',
                niceRow[0].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class ArlingtonLibraries(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/Arlington_Libraries.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                110,
                'library',
                niceRow[0].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class ArlingtonNatureCenters(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/Arlington_Nature_Centers.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                110,
                'nature center',
                niceRow[0].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class ArlingtonSchools(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/Arlington_Schools.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                110,
                'school',
                niceRow[0].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VBElementarySchools(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/VB_Elementary_Schools.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                72,
                'school',
                niceRow[4].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VBGolfCourses(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/VB_Golf_Courses.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                110,
                'golf course',
                niceRow[4].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VBHighSchools(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/VB_High_Schools.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                72,
                'school',
                niceRow[4].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VBLibraries(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/VB_Libraries.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                72,
                'library',
                niceRow[4].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VBMiddleSchools(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/VB_Middle_Schools.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                72,
                'school',
                niceRow[4].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VBMuseums(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/VB_Museums_and_Historic_Sites.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                72,
                'museum or historical site',
                niceRow[4].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VBParks(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/VB_Parks.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                72,
                'park',
                niceRow[4].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VBRecCenters(CsvMapping):
    def __init__(self, db: PSQL):
        self.lastuid = db.get_highest_uid('location', 'location_id')
        super().__init__(
            db, f'./csvData/locations/VB_Recreation_Centers.csv', ['locations'])

    def copy_data(self):
        # csv rows look like:
        #   OBJECTID[0],ID[1],PARKNAME[2],OWNERSHIP[3]...other stuff we don't want
        for row in self.csvData:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make row
            self.lastuid += 1  # increment id

            location_row = [
                self.lastuid,
                72,
                'rec center',
                niceRow[4].replace('\'', '\'\'')
            ]
            # insert things
            self.db.insert('location', location_row)


class VACounties(CsvMapping):
    def __init__(self, db: PSQL):
        super().__init__(
            db, f'./csvData/county.csv', ['county'])

    def copy_data(self):

        # add csv for square area collumn
        self.add_extra_csv('./csvData/county_sqrarea.csv')

        # store extra csv so it can be read multiple times
        self.csvDataList[1] = [r for r in self.csvDataList[1]]

        # csv rows look like:
        #   [0] county_id, [1] station_id, [2] county_name, [3] lat, [4] lon
        #
        #   [0] county, [1] fips code, [2] county seat, [3] established, [4] origin,
        #       [5] etymology, [6] population, [7] area, [8] map
        for row in self.csvDataList[0]:
            niceRow = [s.strip() for s in row]  # trim unncessary whitespace

            # make sure rows are valid (for this csv)
            if len(niceRow) < 5:
                continue

            # go through secondary csv to look for square miles
            formatted_county_name = niceRow[2]
            if 'City' in formatted_county_name:  # remove 'city' for matching
                formatted_county_name = formatted_county_name.replace(
                    'City', '').strip()
            sqr_miles = 0
            for r in self.csvDataList[1]:
                # make sure row is valid (for secondary csv)
                if len(r) < 9:
                    continue
                nR = [s.strip() for s in r]  # trim unncessary whitespace
                if nR[0] == formatted_county_name:
                    sqr_miles = nR[7]  # get area for county with matching name
                    break

            # make row ('naively' assume that county_id and station_id in the csv are accurrate)
            county_row = [
                niceRow[0],  # county_id
                niceRow[1],  # station_id
                niceRow[2],  # county_name
                niceRow[3],  # lat
                niceRow[4],  # long
                sqr_miles,  # sqr miles
            ]

            log.debug(county_row)

            # insert things
            self.db.insert('county', county_row)

    def county_exists(self, county_name: str) -> int:
        # if county exists, return its id, else return None
        sql = f'SELECT county_id FROM county WHERE county_name = \'{county_name}\''
        county_id = self.db.select(sql)
        if county_id:
            return county_id[0][0]
        else:
            return None


if __name__ == '__main__':
    main()
