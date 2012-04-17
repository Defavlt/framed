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
<p>404 Not Found: What you searched for was not found:</p>
EOT;
		echo "on: " . $on . "<br>";
		echo "id: " . $id . "<br>";
		echo "msg: " . $msg . "<br>";
		echo $output . \CONFIGURATION::$BASE_URL . $on . "/" . $id;
	}
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
		\crm::gInstance()->Register($this, \MESSAGES::ERROR_404);
		\crm::log("_404", IPlugin);
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