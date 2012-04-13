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

spl_autoload_extensions(php);
spl_autoload_register("__autoload");

function __autoload($name) {
	
	$name = str_replace("\\", DIRECTORY_SEPARATOR, $name);
	$name = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $name);
	$name = BASE . $name . php;
	
	if (is_file($name)) {
		require_once $name;
	}
	else {
		
		error_log($name, 0);
	}
}

crm::Start();

?>