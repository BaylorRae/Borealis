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

$Map->connect('/test_page');

?>