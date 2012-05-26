<?php
namespace plugins;

use constants\PLUGIN_VISIBILITY;
use interfaces\IObserver;
use interfaces\IPlugin;

/**
 *
 * @author marcus
 *        
 */
class index implements IObserver, IPlugin {

	function gVisibility() {
		
		return PLUGIN_VISIBILITY::PU;
	}
	
	function Initialize() {
		
		\crm::gInstance()->Register($this, "index");
		\crm::log("index", IPlugin);
	}
	function Plugin() {}
	
	function Callback($on, $id, $msg) {
		
		ob_start();

		
		

		echo ob_get_clean();
		
	}

}

?>
