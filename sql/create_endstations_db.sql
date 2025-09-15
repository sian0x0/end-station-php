CREATE DATABASE endstation_db;
CREATE TABLE endstation_db.endstations LIKE vbb_gtfs.endstations;

INSERT INTO endstation_db.endstations
SELECT * FROM vbb_gtfs.endstations;

CREATE TABLE endstation_db.users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL,
    password CHAR(60) NOT NULL,
    role TINYINT NOT NULL
);
