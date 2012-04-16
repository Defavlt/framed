<?php

/** 
 * @author marcus
 * 
 */
final class CONFIGURATION {
	const log = "log";
	
	static $EXTRA_CONF = "extra_conf";
	
	/**
	 * The directory containing all the plugins for use.
	 * @var string
	 */
	static $PLUGIN_DIR = "plugins";
	
	/**
	 * The application directory.
	 * CHANGE WITH CAUTION
	 * @var string
	 */
	static $BASE_DIR = "base_dir";
	
	/**
	 * The base (public) url of the application
	 * @var string
	 */
	static $BASE_URL = "base_url";
	
	/**
	 * The available actions the application can do.
	 * @var string
	 */
	static $ACTIONS = "actions";
	
	/**
	 * The plugins available for the application
	 * @var string
	 */
	static $PLUGINS = "plugins";

	static $METHOD = "method";
	static $OBJECT = "object";
	static $ACTION = "action";
	static $IDENTI = "identi";
	
	static $LOGDIR = "logs";
	static $STDOUT = "stdout";
	static $LOGS = "stdout";
}

?>