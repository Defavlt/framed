<?php
namespace interfaces;

/**
 * Makes it possible to add external functionality to the system.
 * @author marcus
 *        
 */
interface IPlugin {
	
	/**
	 * The visibility of this (can it be invoked by the user, from the webbrowser?).
	 * @return PLUGIN_VISIBILITY
	 */
	function gVisibility();
	
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