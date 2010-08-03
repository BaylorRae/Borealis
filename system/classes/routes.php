<?php

class Routes extends Base {
  
  private $default_options = array(
      'default_path' => ':name.:format',
      'formats' => array(
          'html' => 'php' // page.html will load page.php
      ),
      'default_page' => 'index',
      'default_format' => 'html'
    );
    
  protected $options;
  private $connections;
  private $path;
  
  private $dynamic_vars = array();
  
  /**
   * Set the options for handling routes
   *
   * @param array $options
   * @return void
   * @author Baylor Rae'
   */
  public function config($options) {
    if( is_array($options) )
      $this->options = $options;
    else
      die('<br />Make sure you supplied an array for the config in <code>config/routes.php</code>');
  }
  
  /**
   * Custom URL paths to connect to files
   *
   * @param string $path The custom path (/add_comment)
   * @param array $options The options for the path array( 'filename', [ 'allow_formats' => true ] )
   * @return void
   * @author Baylor Rae'
   */
  public function connect($path, $options = null) {
    $options = (empty($options)) ? array() : $options;
    if( is_array($options) ) {      
            
      $this->connections[] = array_merge(
        array('route' => $path),
        $options
      );
                    
    }else
      die('<br />Make sure you are using an array for the options in <code>config/routes.php</code>');
    
  }
  
  private function run_connections() {
    $this->connect('/' . $this->option('default_page'), array('filename' => ''));
    $connections = $this->connections;
        
    if( isset($_GET['borealis_url']) )
      $page = '/' . $_GET['borealis_url'];
    else
      $page = '/' . $this->option('default_page');
           
     $paths = explode('/', $page);
          
    
    foreach( $connections as $connection ) {
      $connection = (object) $connection;
      $parts = explode('/', $connection->route);
      
      // Make sure the urls match
      if( $this->match($paths, $parts) ){
        $this->path = (object) array_merge((array) $connection, array('path' => $page));
      }else
        continue;
            
    }
    
  }
  
  /**
   * Loads the file or the custom page
   *
   * @return void
   * @author Baylor Rae'
   */
  public function dispatch() {
    $this->run_connections();
    
    // Check for dynamic parameters
    if( count($this->dynamic_vars) ) {
      $path = explode('/', $this->path->path);
      
      
      foreach( $this->dynamic_vars as $id => $data ) {
        
        if( isset($path[$id]) ) {
          foreach( $data as $key => $value ) {
            
            // Remove the paramater from the url
            $this->path->path = str_replace('/' . $value, '', $this->path->path);
          }
        }
      }
    }
                      
    // Allow formats 
    if( !array_key_exists('allow_formats', $this->path) )
      $this->path->allow_formats = true;
    
  
  
    // Remove ending slash
    $this->path->path = trim($this->path->path, '/');
  
  
    
    // Check for a format
    if( preg_match('/\.(\w+)$/', $this->path->path, $match) )
      $format = $match[1];
  
  
    // Does this view have a default format
    elseif( !empty($this->path->default_format) )
      $format = $this->path->default_format;
    
  
    // Use the global format
    else
      $format = $this->option('default_format');
  
    // Convert the format
    $this->path->format = $this->convert_format($format);
    
    // Remove the format
    $this->path->path = rtrim($this->path->path, '.' . $this->path->format);
    
    
    $this->load($this->path->path, $this->path->format);
      
    
  }
  
  /**
   * Gets the options from $Map->config()
   *
   * @param string $name 
   * @return value if found, false if not
   * @author Baylor Rae'
   */
  protected function option($name) {
    if( !$this->options ) 
      $options = $this->default_options;
    else
      $options = $this->options;
    
    if( !empty($options[$name]) )
      return $options[$name];
    
    elseif( !empty($this->default_options[$name]))
      return $this->default_options[$name];
      
    else
      return false;
  }
  
  /**
   * Make sure the url and the route match
   *
   * @param array $path The url
   * @param array $parts The route
   * @return boolean
   * @author Baylor Rae'
   */
  protected function match($path, $parts) {
    
    foreach( $parts as $id => $part ) {
      $path[$id] = preg_replace('/(\.\w+)$/', '', $path[$id]);
      if( $this->_count($path) == $this->_count($parts) ) {
        
         if( !empty($part) && !empty($path[$id]) ) {
           
           if( $part[0] == ':' ) {
             $this->dynamic_vars[$id] = array(
                 str_replace(':', '', $part) => $path[$id]
               );
             continue;
           }
                     
           if( $path[$id] != $part )
             return false;
           else
             continue;
         }
                   
       }else
        return false;
            
    }
    
    return true;
  }
  
  /**
   * Designed to count the number of segments in a route or path
   *
   * @param array $item 
   * @return integer
   * @author Baylor Rae'
   */
  protected function _count($item) {
    
    $i = 0;
    
    foreach( $item as $value ) {
      if( !empty($value) )
        $i++;
    }
    
    return $i;
  }
  
  /**
   * Convert a format to the new format
   *
   * @param string $name 
   * @return string
   * @author Baylor Rae'
   */
  protected function convert_format($name) {
    $format = $this->option('formats');
    if( isset($format[$name]) )
      return $format[$name];
    else
      return $name;
  }
  
  /**
   * Reverts a format back to its original
   *
   * @param string $name 
   * @return string
   * @author Baylor Rae'
   */
  protected function revert_format($name) {
    $formats = $this->option('formats');
    foreach( $formats as $orig => $custom ) {
      if( $name == $custom )
        return $orig;
    }
    return $name;
  }

  /**
   * Loads the dynamic page or the "physical" page
   *
   * @param string $filename 
   * @param string $format 
   * @return void
   * @author Baylor Rae'
   */
  protected function load($filename, $format) {
    $format = $this->revert_format($format);
    
    if( file_exists(BASE_PATH . '/app/public_pages/' . $filename . '.php') ) {
      include BASE_PATH . '/app/public_pages/' . $filename . '.php';
      
      if( class_exists($filename) ) {
        
        if( method_exists($filename, $format) ) {
          $class = new $filename;
                    
          $class->params = $this->params();
          Base::$variables[] = array('params', $this->params());
                                                  
          $class->$format();
                              
          if( count(Base::$variables) ) {
            foreach( Base::$variables as $var => $value ) {
              $$value[0] = $value[1];
            }
          }
                    
          if( file_exists(BASE_PATH . '/public/' . $filename . '.' . $this->convert_format($format) ) )
            include BASE_PATH . '/public/' . $filename . '.' . $this->convert_format($format);
        }
        
        // This method doesn't exist so load the real file
        elseif( file_exists(BASE_PATH . '/public/' . $filename . '.' . $this->convert_format($format) ) ) {
          
          Base::$variables[] = array('params', $this->params());
          if( count(Base::$variables) ) {
            foreach( Base::$variables as $var => $value ) {
              $$value[0] = $value[1];
            }
          }
          
          include BASE_PATH . '/public/' . $filename . '.' . $this->convert_format($format);
        }
 
        // The real file doesn't exist either
        else
          die('<br />Make sure you have created your default page at <code>' . BASE_PATH . '/public/' . $filename . '.' . $this->convert_format($format) . '</code>');
        
      }else
        die('<br />Make sure you created the class <code>' . $filename . '</code> in <code>' . BASE_PATH . '/app/public_pages/' . $filename . '.php</code>');
    
    } // Not a dynamic file
    
    
    elseif( file_exists(BASE_PATH . '/public/' . $filename . '.' . $this->convert_format($format) ) )
      include BASE_PATH . '/public/' . $filename . '.' . $this->convert_format($format);
      
      
    else
      die('<br />Make sure you have created your default page at <code>' . BASE_PATH . '/public/' . $filename . '.' . $this->convert_format($format) . '</code>');
  }
  
  /**
   * Creates an array of parameters from $_GET, $_POST, and dynamic
   *
   * @return array
   * @author Baylor Rae'
   */
  protected function params() {
    
    $output = array();
    
    // Get the dynamic
    foreach( $this->dynamic_vars as $id => $data ) {
      foreach( $data as $var => $value ) {
        $output[$var] = $value;
      }
    }
    
    // Add the format
    $output['format'] = $this->revert_format($this->path->format);
    
    // $_POST
    foreach( $_POST as $var => $value ) {
      $output[$var] = $value;
    }
    
    // $_GET
    foreach( $_GET as $var => $value ) {
      if( $var != 'borealis_url' )
        $output[$var] = $value;
    }
            
    return $output;
    
  }
}

