<?php

/** 
 * Contains application-critical configuration properties.
 * @author marcus
 */
use exceptions\NoSuchValueException;

final class CONFIGURATION {
	const log = "log";
	
	static $EXTRA_CONF = "extra_conf";
	static $PLUGIN_DIR = "plugins";
	static $BASE_DIR = "base_dir";
	static $BASE_URL = "base_url";
	static $ACTIONS = "actions";
	static $PLUGINS = "plugins";
	static $METHOD = "method";
	static $OBJECT = "object";
	static $ACTION = "action";
	static $IDENTI = "identi";
	static $DBClassprefix = "DBClassPrefix";
	
	static $DB = "DB";
	static $DB_HOST = "DB_HOST";
	static $DB_USER = "DB_USER";
	static $DB_PASS = "DB_PASS";
	static $DB_PORT = "DB_PORT";
	
	static $LOGDIR = "logs";
	static $STDOUT = "stdout";
	static $LOGS = "stdout";
	
	//For added convenience.
	public static function __callstatic($name, $args) {
		$config = null;
		
		if (isset(self::$$name)) {
			
			return self::$$name;
		}
		else if (!is_null(($config = crm::gConfig($name)))) {
			
			return $config;
		}
		else {
			
			throw new NoSuchValueException();
		}
	}
}

?>