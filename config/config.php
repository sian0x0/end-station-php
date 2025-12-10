<?php

// get constants from .env and concatenate function using define() (not allowed using normal assignment)
define('DB_HOST', getenv('DB_HOST'));
define('DB_PORT', getenv('DB_PORT'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASSWORD'));
define('DB_NAME_MAIN', getenv('DB_NAME_MAIN'));
define('DB_NAME_GTFS', getenv('DB_NAME_GTFS'));

// DSNs built from constants
const DSN_NO_DB = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT;
const DSN_GTFS = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME_GTFS;
const DSN_MAIN = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME_MAIN;

//no local dns - change later
//const DOMAIN_NAME = 'http://www.endstation.sian.web.bbq'; #TODO