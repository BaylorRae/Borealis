<?php

class Base {
  
  public static $variables = array();
  public $params;
  
  /**
   * Get the DB connection info, and connect the the database using PHP ActiveRecord
   * Can use (mysql, sqlite, postgresql, oracle)
   *
   * @return void
   * @author Baylor Rae'
   */
  public function load_db() {
    $DB = array();
    $db_info = null;
    
    include_once BASE_PATH . '/config/database.php';
    
    // Check for environment db connection info
    if( isset($DB[ENVIRONMENT]) ) {
      
      if( isset($DB['defaults']) )
        $db_info = (object) array_merge($DB['defaults'], $DB[ENVIRONMENT]);
      else
        $db_info = (object) $DB[ENVIRONMENT];
        
      if( isset($db_info->type) && in_array($db_info->type, array('mysql', 'sqlite', 'postgresql', 'oracle')) ) {
        
        $connection = null;
        switch ($db_info->type) {
          
          case 'mysql' :
            $connection = array('development' => 'mysql://' . $db_info->username . ':' . $db_info->password . '@' . $db_info->host . '/' . $db_info->name);
          break;
          
          case 'sqlite' :
            $connection = array('development' => 'sqlite://' . BASE_PATH . '/system/db/' . $db_info->file);
          break;
          
          case 'postgresql' :
            $connection = array('development' => 'pgsql://' . $db_info->username . ':' . $db_info->password . '@' . $db_info->host . '/' . $db_info->name);
          break;
          
          case 'oracle' :
            $connection = array('development' => 'oci://' . $db_info->username . ':' . $db_info->password . '@' . $db_info->host . '/' . $db_info->name);
          break;
        }
                
        ActiveRecord\Config::initialize(function($cfg) use ($connection) {
          
          $cfg->set_model_directory(BASE_PATH . '/system/models');
          $cfg->set_connections($connection);
          
        });
        
      }else
        die('<br />Make sure you have set a database type in <code>config/database.php</code> (mysql, sqlite, postgresql, oracle)');
      
    }else
      die('<br />Make sure you have set your environment in <code>config/config.php</code> and your database information in <code>config/database.php</code>');
  }
  
  function __set($name, $value) {
    Base::$variables[] = array($name, $value);
  }
  
}

