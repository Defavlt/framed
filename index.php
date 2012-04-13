<?php 

/**
 * The config-file for the application.
 * @var string
 */
define("CONFIG_FILE", "config.ini");

/**
 * The default application directory.
 * @var string
 */
define("BASE", __DIR__ . DIRECTORY_SEPARATOR);

/**
 * The default file-ending the application uses.
 * @var string
 */
define("php", ".php");

function __autoload($name) {
	
	if (is_file($name)) {
		
		require BASE . $name .php;
	}
}

crm::Start();

?>