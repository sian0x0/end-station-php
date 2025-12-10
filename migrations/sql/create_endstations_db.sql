##Note: create now done by declaring DB_MAIN in dockerfile
# #CREATE DATABASE endstation_db;

#DROP TABLE endstation_db.endstations;
CREATE TABLE endstation_db.endstations LIKE VBB_GTFS.endstations;

INSERT INTO endstation_db.endstations
SELECT * FROM VBB_GTFS.endstations;

USE endstation_db;

##derive station id - numerical part only. set as key
ALTER TABLE endstations ADD COLUMN endstation_id INT;
UPDATE endstations SET endstation_id = CAST(SUBSTRING_INDEX(parent_station, ':', -1) AS UNSIGNED); ##trim and cast to int

## TODO:reinstate keys when fixed
# ALTER TABLE endstations ADD PRIMARY KEY (endstation_id);

CREATE TABLE users (
    user_id INT, #TODO: add back auto/key
    username VARCHAR(20) NOT NULL,
    password CHAR(60) NOT NULL,
    join_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    profile_picture VARCHAR(255) DEFAULT NULL,
    role TINYINT NOT NULL
);

INSERT INTO users (user_id, username, password, join_date, profile_picture, role) VALUES
     (1, 'admin', 'hashed_password_123', '2025-05-15', 'public/assets/img/profile/1.jpg', 1),
     (2, 'lou', 'hashed_password_456', '2025-07-22', 'public/assets/img/profile/2.jpg', 2),
     (3, 'vullnet', 'hashed_password_789', '2025-09-10', 'public/assets/img/profile/3.jpg', 2);

CREATE TABLE visits (
                        visit_id INT, #TODO: add back auto/key
                        user_id INT NOT NULL,
                        endstation_id INT NOT NULL,
                        guest_ids varchar(255),
                        visit_datetime DATETIME,
                        photo VARCHAR(255),
                        notes TEXT
);

#ALTER TABLE visits
    #still need to implement station ID from the original data (edit parent_station to just the int part)
#     ADD CONSTRAINT fk_visits_user
#         FOREIGN KEY (user_id) REFERENCES users(id);

    ##TODO: re-add constraints when everything working
# ALTER TABLE visits
#     ADD CONSTRAINT fk_visits_endstation
#         FOREIGN KEY (endstation_id) REFERENCES endstations(endstation_id);

-- Add test data for user '..?', user_id = 2
INSERT INTO visits (user_id, endstation_id, visit_datetime, photo, notes) VALUES

(1, 900350160, '2025-06-12 09:15:00', NULL, NULL),
(2, 900245027, '2025-06-12 09:15:00', NULL, 'Vanilla cone while waiting for the next train'),
(3, 900083201, '2025-11-03 14:22:00', NULL, 'Strawberry gelato at the kiosk'),
(1, 900350160, '2025-11-03 14:22:00', NULL, NULL),
(2, 900350160, '2025-02-18 17:45:00', NULL, NULL),
(3, 900350160, '2025-02-18 17:45:00', NULL, 'Mint-choc chip before heading home'),
(2, 900083201, '2025-09-29 12:05:00', NULL, 'Salted-caramel ice cream break'),
(2, 900245027,  '2025-07-01 19:30:00', NULL, NULL);
