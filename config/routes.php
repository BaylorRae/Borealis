<?php

$Map->config(array(
    'default_path' => ':name.:format',
    
    // Format translation
    'formats' => array(
      'html' => 'php' // calling page.html will load page.php
    ),
    
    'default_page' => 'index',
    'default_format' => 'html'
  ));

  
$Map->connect('/add_comment/:action/:id', array(
    'filename' => 'add_comment'
  ));
  
$Map->connect('/add_comment/:action', array(
    'filename' => 'add_comment'
  ));

$Map->connect('/add_comment', array(
    'filename' => 'add_comment'
  ));

?>