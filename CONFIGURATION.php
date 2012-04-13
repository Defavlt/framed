<?php

/** 
 * @author marcus
 * 
 */
final class CONFIGURATION {
	
	static $EXTRA_CONF = "extra_conf";
	
	/**
	 * The directory containing all the plugins for use.
	 * @var string
	 */
	static $PLUGIN_DIR = "plugin_dir";
	
	/**
	 * The application directory.
	 * CHANGE WITH CAUTION
	 * @var string
	 */
	static $BASE_DIR = "base_dir";
	
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
	
	/**
	 * The fallback message type to use 
	 * (e.g. whenever something that doesn't exist is requested (404 etc.))
	 * @var string
	 */
	static $FALLBACK = "fallback";
}

?>