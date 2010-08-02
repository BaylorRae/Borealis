<?php

$DB['defaults'] = array(
    'host' => 'localhost',
    'type' => 'mysql' // mysql, sqlite, postgresql, oracle
  );

$DB['development'] = array(
    'username' => 'root',
    'password' => 'root',
    'name' => 'adminplus'
  );
  
$DB['production'] = array(
    'username' => '',
    'password' => '',
    'name' => ''
  );

?>