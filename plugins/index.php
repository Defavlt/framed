<?php
namespace plugins;

use interfaces\IPlugin;
use interfaces\IObserver;
use settings\PLUGIN_VISIBILITY;

/**
 *
 * @author marcus
 *        
 */
class index implements IPlugin, IObserver {
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
	
	}
	
	/**
	 *
	 * @see IPlugin::Plugin()
	 *
	 */
	public function Plugin() {
		\crm::Register($this, \MESSAGES::INDEX);
		\crm::log("index", IPlugin);
	}
	
	/**
	 *
	 * @return PLUGIN_VISIBILITY
	 *
	 * @see IPlugin::gVisibility()
	 *
	 */
	public function gVisibility() {
		return PLUGIN_VISIBILITY::PU;
	}
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
		echo "index, default page.";
		\crm::gInstance()->SendMessage(\MESSAGES::ERROR_404, "index", null);
	}
}

?>