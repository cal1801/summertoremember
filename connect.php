<?php

//PCCCA

//New Database Info
define('DB_USER','root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');
define('DB_NAME', 'pccca_sites');

/*
//Old MySql 5 Database
define('DB_USER','dbo463510694');
define('DB_PASSWORD', 'f8gEGBCV');
define('DB_HOST', 'db463510694.db.1and1.com');
define('DB_NAME', 'db463510694');
*/


/*
// DB old MySQL4
define('DB_USER','dbo271160374');
define('DB_PASSWORD', 'f8gEGBCV');
define('DB_HOST', 'db1788.perfora.net');
define('DB_NAME', 'db271160374');
*/


$db = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) OR die ('Could not connect to the database: '.mysql_error());
@mysql_select_db(DB_NAME) OR die ('Could not select the database: '.mysql_error());

// also set default timezone for all portions of website
date_default_timezone_set("America/New_York");

?>