<?php

/**
 * Comment this line (or set the value to '0') if you don't want debug capabilities
 * Defining this constant enables error-reporting during configuration loadout.
 * @var int
 */
define("DEBUG", 1);

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
define("NEWLINE", "\n");
define("NEWBLOCK", "\0");

spl_autoload_extensions(php);
spl_autoload_register("__autoload");

function __autoload($name) {

	$pattern = <<<HTML
/\\\\/
HTML;
	
	$_name = preg_replace($pattern, DIRECTORY_SEPARATOR, $name);
	$_name = BASE . $_name . php;
	
	if (is_file($_name)) {

		require_once $_name;
	}
	else {
		error_log("Failed to include required file: " . $_name, 0);
	}
}

crm::Start();

?>
