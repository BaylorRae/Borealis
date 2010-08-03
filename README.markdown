Borealis is still being built, but if you want to work on it here is the run down.

### Database
All database interactions are handled with [PHP ActiveRecord](http://www.phpactiverecord.org/) and the models are place in `system/models`

### Routes
Routes are created in `config/routes.php` with the following syntax.
  
	$Map->connect('/product/:slug', array(
		
	  // (optional) (default = true) allows different types of formats (.json, .xml)
	  'allow_formats' => true,
	
	  // (optional) (defaults to config) the default format to load
	  'default_format' => 'json'
	));

The first parameter is the route and the second are the options. The above code includes all the options currently available.

Inside the route you may notice `:slug`. This will create a variable accessed via `$params['slug']`

If you went to `http://example.com/product/ballpoint-pen` in your browser Borealis would try to load the product page in the following hierarchy.

1. `app/public_pages/product.php` (optional)
2. `public/product.php`

The first file is used to make a dynamic page that doesn't exist on the server. The file requires the following class

	<?php
	
	// The name of the class must match the page name
	class product extends Base {
	
	  // The functions inside the class represent each format
	  function html() {
		
		// This will create the variable "$hello_world" available to us in "public/product.php"
	    $this->hello_world = 'hi';
	
		// We can also access the params variable
		if( $this->params['slug'] )
			echo $this->params['slug'];
	  }

	  function json() {

	  }

	}

If you create a dynamic page Borealis will automatically try to load the file `public/product.php` if the file exists

## TODO

1. Make it easier to add models
2. Start working on the admin page

*This isn't everything to do, just the important stuff right now*
