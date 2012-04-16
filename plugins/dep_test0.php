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
class dep_test0 implements IObserver, IPlugin {
	const header1 = "Customers:<br>";
	const header2 = "Generated on: ";
	const msg = "dep_test0";
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
		echo dep_test0::header1;
		
		\crm::dep_test1(5, "inject");
		
		echo dep_test0::header2 . date();
	}
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
		\crm::Register($this, dep_test0::msg);
		\crm::log(dep_test0::msg, IPlugin);
	}
	
	/**
	 *
	 * @see IPlugin::Plugin()
	 *
	 */
	public function Plugin() {
	
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
}

?>