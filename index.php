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
	
	$names = explode(DIRECTORY_SEPARATOR, $name);
	$length = count($names);
	
	$name = BASE . $names[$length - 1] . php;
	
	if (is_file($name)) {
		
		file_put_contents("php://stderr", $name);
		require $name;
	}
}

crm::Start();

?>