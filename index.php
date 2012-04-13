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
	
	$name = str_replace("\\\\", DIRECTORY_SEPARATOR, $name);
	
	error_log(is_file(BASE . $name . php, 0));
	
	if (is_file($name)) {
		require_once BASE . $name . php;
	}
	else {
		
		error_log(BASE . $name . php, 0);
	}
}

crm::Start();

?>