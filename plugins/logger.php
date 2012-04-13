<?php
namespace plugins;

use interfaces\IObserver;
use interfaces\IPlugin;

/**
 *
 * @author marcus
 *        
 */
class logger implements IPlugin, IObserver {
	
	function Initialize() {
		\crm::getCurrent()->Register($this, \CONFIGURATION::log);
	}
	function Plugin() {
		;
	}
	
	function Callback($on, $id) {
		
		error_log($on, 0);
	}

}

?>