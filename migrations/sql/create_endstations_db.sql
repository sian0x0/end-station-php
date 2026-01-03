##Note: create now done by declaring DB_MAIN in dockerfile and tables in init.sql
# #CREATE DATABASE endstation_db;

#DROP TABLE endstation_db.endstations;

#TODO: reinstate this when VBB GTFS DB working again
#CREATE TABLE endstation_db.endstations LIKE VBB_GTFS.endstations;
#INSERT INTO endstation_db.endstations
##SELECT * FROM VBB_GTFS.endstations;

USE endstation_db;

## ADD TEST DATA for users and visits --stations comes from JSON -- TODO: stations must come before visits when FKs implemented
INSERT INTO users (user_id, username, password, join_date, profile_picture, role) VALUES
     (1, 'admin', '$2y$10$jU4yKmRPl0cqTti/LeI3HOL/oy12nyHAlWnEnRF6ahZdOv3hHcqva', '2025-05-15', '1.jpg', 1),
     (2, 'lou', '$2y$10$UTWwQGn9wbJZGFGc0z7MwOAEAK3VvC6YOMOX2Y54dCy0aV7SCLmRO', '2025-07-22', '2.jpg', 2),
     (3, 'vullnet', '$2y$10$cl6ng.EVDcNgzWnmDuQXQejv7RtPOQ1sADXqSV43cjs24Lrby/JEy', '2025-09-10', '3.jpg', 2);

INSERT INTO visits (user_id, endstation_id, visit_datetime, photo, notes) VALUES
(1, 900350160, '2025-06-12 09:15:00', NULL, NULL),
(2, 900245027, '2025-06-12 09:15:00', NULL, 'Vanilla cone while waiting for the next train'),
(3, 900083201, '2025-11-03 14:22:00', NULL, 'Strawberry gelato at the kiosk'),
(1, 900350160, '2025-11-03 14:22:00', NULL, NULL),
(2, 900350160, '2025-02-18 17:45:00', NULL, NULL),
(3, 900350160, '2025-02-18 17:45:00', NULL, 'Mint-choc chip before heading home'),
(2, 900083201, '2025-09-29 12:05:00', NULL, 'Salted-caramel ice cream break'),
(2, 900245027,  '2025-07-01 19:30:00', NULL, NULL);

### derive station id - numerical part only. set as key
##ALTER TABLE endstations ADD COLUMN endstation_id INT;  ##no longer needed - table no longer created as a copy

UPDATE endstations SET endstation_id = CAST(SUBSTRING_INDEX(parent_station, ':', -1) AS UNSIGNED); ##trim and cast to int - must come after data import (duplicated at json defaults read)

## TODO:reinstate keys and constraints when everything working
# ALTER TABLE endstations
#   ADD PRIMARY KEY (endstation_id);

#ALTER TABLE visits
#     ADD CONSTRAINT fk_visits_user
#         FOREIGN KEY (user_id) REFERENCES users(id);

# ALTER TABLE visits
#     ADD CONSTRAINT fk_visits_endstation
#         FOREIGN KEY (endstation_id) REFERENCES endstations(endstation_id);
