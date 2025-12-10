## one-time setup for underlying databases in MariaDB container

## create databases
## #TODO: add the rest of db creation (tables and maybe initial import) here instead
CREATE DATABASE IF NOT EXISTS endstation_db;

CREATE DATABASE IF NOT EXISTS VBB_GTFS;

USE VBB_GTFS;

DROP TABLE IF EXISTS agency;

CREATE TABLE `agency` (
                          agency_id VARCHAR(50) PRIMARY KEY,
                          agency_name VARCHAR(255),
                          agency_url VARCHAR(255),
                          agency_timezone VARCHAR(50),
                          agency_lang VARCHAR(50),
                          agency_phone VARCHAR(50)
);

DROP TABLE IF EXISTS calendar;

CREATE TABLE `calendar` (
                            service_id VARCHAR(50),
                            monday TINYINT(1),
                            tuesday TINYINT(1),
                            wednesday TINYINT(1),
                            thursday TINYINT(1),
                            friday TINYINT(1),
                            saturday TINYINT(1),
                            sunday TINYINT(1),
                            start_date VARCHAR(8),
                            end_date VARCHAR(8),
                            KEY `service_id` (service_id)
);

DROP TABLE IF EXISTS calendar_dates;

CREATE TABLE `calendar_dates` (
                                  service_id VARCHAR(50),
                                  `date` VARCHAR(8),
                                  exception_type INT(2),
                                  KEY `service_id` (service_id),
                                  KEY `exception_type` (exception_type)
);

DROP TABLE IF EXISTS fare_attributes;

DROP TABLE IF EXISTS fare_rules;

DROP TABLE IF EXISTS routes;

CREATE TABLE `routes` (
                          route_id VARCHAR(50) PRIMARY KEY,
                          agency_id VARCHAR(50),
                          route_short_name VARCHAR(50),
                          route_long_name VARCHAR(255),
                          route_type INT(4),
                          route_color VARCHAR(50),
                          route_text_color VARCHAR(50),
                          route_desc VARCHAR(255),
                          KEY `route_type` (route_type)
);

DROP TABLE IF EXISTS shapes;

CREATE TABLE `shapes` (
                          shape_id VARCHAR(50),
                          shape_pt_lat DECIMAL(8,6),
                          shape_pt_lon DECIMAL(8,6),
                          shape_pt_sequence INT(11),
                          KEY `shape_id` (shape_id)
);

DROP TABLE IF EXISTS stop_times;

CREATE TABLE `stop_times` (
                              trip_id VARCHAR(50),
                              stop_id VARCHAR(50),
                              stop_sequence INT(11),
                              pickup_type INT(2),
                              drop_off_type INT(2),
                              stop_headsign VARCHAR(255),
                              arrival_time VARCHAR(8),
                              departure_time VARCHAR(8),
                              KEY `trip_id` (trip_id),
                              KEY `stop_id` (stop_id),
                              KEY `stop_sequence` (stop_sequence)
);

DROP TABLE IF EXISTS stops;
CREATE TABLE `stops` (
                         stop_id VARCHAR(50) PRIMARY KEY,
                         stop_code VARCHAR(10),
                         stop_name VARCHAR(255),
                         stop_desc VARCHAR(255),
                         stop_lat DECIMAL(8,6),
                         stop_lon DECIMAL(8,6),
                         location_type INT(2),
                         parent_station VARCHAR(255),
                         wheelchair_boarding INT(2),
                         platform_code VARCHAR(20),
                         zone_id VARCHAR(255),
                         level_id INT(4),
                         KEY `stop_lat` (stop_lat),
                         KEY `stop_lon` (stop_lon)
);

DROP TABLE IF EXISTS transfers;

CREATE TABLE `transfers` (
                             from_stop_id VARCHAR(50),
                             to_stop_id VARCHAR(50),
                             transfer_type VARCHAR(50),
                             min_transfer_time VARCHAR(50),
                             from_route_id VARCHAR(50),
                             to_route_id VARCHAR(50),
                             from_trip_id VARCHAR(50),
                             to_trip_id VARCHAR(50),
                             KEY `from_stop_id` (from_stop_id),
                             KEY `to_stop_id` (to_stop_id)
);

DROP TABLE IF EXISTS trips;

CREATE TABLE `trips` (
                         route_id VARCHAR(50),
                         service_id VARCHAR(50),
                         trip_id VARCHAR(50) PRIMARY KEY,
                         trip_headsign VARCHAR(255),
                         trip_short_name VARCHAR(50),
                         direction_id TINYINT(1),
                         block_id VARCHAR(50),
                         shape_id VARCHAR(50),
                         wheelchair_accessible INT(2),
                         bikes_allowed INT(2),
                         KEY `route_id` (route_id),
                         KEY `service_id` (service_id),
                         KEY `direction_id` (direction_id),
                         KEY `shape_id` (shape_id)
);

##TODO:create table here instead
##USE endstation_db;


## create users and grant privileges
GRANT ALL PRIVILEGES ON endstation_db.* TO 'user101'@'%';
GRANT ALL PRIVILEGES ON VBB_GTFS.* TO 'user101'@'%';
GRANT CREATE, DROP ON *.* TO 'user101'@'%';
FLUSH PRIVILEGES;


##data is inserted by file gtfs_to_mysql.sql...