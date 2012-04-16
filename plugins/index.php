<?php
namespace plugins;

use interfaces\IPlugin;
use interfaces\IObserver;
use constants\PLUGIN_VISIBILITY;

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
		\crm::gInstance()->Register($this, "index");
		crm::gInstance()->debug();
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
	}
}

?>