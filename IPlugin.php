<?php
namespace interfaces;

/**
 *
 * @author marcus
 *        
 */
interface IPlugin {
	
	/**
	 * This is invoked when all the existing plugins and the main application has been loaded.
	 * Can and should be used to resolve any dependancies and/or register any needed listeners. 
	 */
	function Initialize();
	
	/**
	 * This is invoked when the plugin is loaded.
	 * Plugin() should instantiate any internal and/or static resources needed.
	 */
	function Plugin();
}

?>