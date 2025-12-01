##Note: create now done by declaring DB_MAIN in dockerfile
# #CREATE DATABASE endstation_db;

CREATE TABLE endstation_db.endstations LIKE vbb_gtfs.endstations;

INSERT INTO endstation_db.endstations
SELECT * FROM vbb_gtfs.endstations;

USE endstation_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL,
    password CHAR(60) NOT NULL,
    join_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    profile_picture VARCHAR(255) DEFAULT NULL,
    role TINYINT NOT NULL
);

INSERT INTO users (username, password, role) VALUES
     ('admin', 'ilovetrains', 1),
     ('lou', 'ilovetrains', 2),
     ('vullnet', 'ilovetrains', 2);

CREATE TABLE visits (
                        visit_id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        endstation_id INT NOT NULL,
                        visit_datetime DATETIME,
                        photo VARCHAR(255),
                        notes TEXT
);

ALTER TABLE visits
    #still need to implement station ID from the original data
#     ADD CONSTRAINT fk_visits_user
#         FOREIGN KEY (user_id) REFERENCES users(id);

ALTER TABLE visits
    ADD CONSTRAINT fk_visits_endstation
        FOREIGN KEY (endstation_id) REFERENCES endstations(endstation_id);

-- Add test data for user 'ilovetrains', user_id = 2
INSERT INTO visits (user_id, endstation_id, visit_datetime, photo, notes) VALUES
-- Block 1, 2  -> same day
(2, 1, '2024-06-12 09:15:00', NULL, NULL),
(2, 2, '2024-06-12 09:15:00', NULL, 'Vanilla cone while waiting for the next train'),

-- Block 7, 8  -> same day
(2, 7, '2023-11-03 14:22:00', NULL, 'Strawberry gelato at the kiosk'),
(2, 8, '2023-11-03 14:22:00', NULL, NULL),

-- Block 16, 17 -> same day
(2, 16, '2024-02-18 17:45:00', NULL, NULL),
(2, 17, '2024-02-18 17:45:00', NULL, 'Mint-choc chip before heading home'),

-- Single visits
(2, 11, '2023-09-29 12:05:00', NULL, 'Salted-caramel ice cream break'),
(2, 5,  '2024-07-01 19:30:00', NULL, NULL);
