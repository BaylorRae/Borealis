<?php

// Prepare the global variables
define('BASE_PATH', realpath(dirname(__FILE__)));

// Get the main files
include_once 'config/config.php';
include_once 'system/classes/base.php';
include_once 'system/libraries/php-activerecord/ActiveRecord.php';

// Initialize the base class
$Base = new Base();

// Connect the the database
$Base->load_db();

?>