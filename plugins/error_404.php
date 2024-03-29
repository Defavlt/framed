<?php
namespace plugins;

use constants\PLUGIN_VISIBILITY;
use constants\MESSAGES;
use interfaces\IObserver;
use interfaces\IPlugin;

/**
 * Need module for 
 * @author marcus
 *        
 */
class error_404 implements IObserver, IPlugin {
	
	/**
	 * @see interfaces.IPlugin::gVisibility()
	 */
	function gVisibility() {
		return PLUGIN_VISIBILITY::PU;
	}
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
	//old, obj, new(404)
		$output = <<<'EOT'
<h1 style="color: red">404</h1>
<p>What you searched for was not found:</p>
EOT;

		echo $output . \CONFIGURATION::$BASE_URL . $on . "/" . $id;
	}
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
		\crm::gInstance()->Register($this, MESSAGES::ERROR_404);
		\crm::log(MESSAGES::ERROR_404, IPlugin);
	}
	
	/**
	 *
	 * @see IPlugin::Plugin()
	 *
	 */
	public function Plugin() {
		
	}
}

?>