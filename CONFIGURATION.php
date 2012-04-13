<?php

/** 
 * @author marcus
 * 
 */
final class CONFIGURATION {
	
	const EXTRA_CONF = "extra_conf_files";
	
	/**
	 * The directory containing all the plugins for use.
	 * @var string
	 */
	static $PLUGIN_DIR = "app_plugin_dir";
	
	/**
	 * The application directory.
	 * CHANGE WITH CAUTION
	 * @var string
	 */
	static $BASE_DIR = "app_base_dir";
	
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

	static $METHOD = "default_do_method";
	static $OBJECT = "object_ident_url";
	static $ACTION = "action_ident_url";
	static $IDENTI = "identi_ident_url";
	
	/**
	 * The fallback message type to use 
	 * (e.g. whenever something that doesn't exist is requested (404 etc.))
	 * @var string
	 */
	static $FALLBACK = "fallback";
}

?>