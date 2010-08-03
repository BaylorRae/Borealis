<?php

// Prepare the global variables
define('BASE_PATH', realpath(dirname(__FILE__)));


// Get the main files
include_once 'system/classes/base.php';
include_once 'system/classes/routes.php';
include_once 'system/libraries/php-activerecord/ActiveRecord.php';

// Initialize the base classes
$Base = new Base();
$Map = new Routes();


// Include config files
// Include config files
include_once 'config/config.php';
include_once 'config/routes.php';


// Connect the the database
$Base->load_db();


// Load the page
$Map->dispatch();

?>