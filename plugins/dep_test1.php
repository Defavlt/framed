<?php
namespace plugins;

use interfaces\IObserver;
use interfaces\IPlugin;

/**
 *
 * @author marcus
 *        
 */
class dep_test1 implements IObserver, IPlugin {
	const msg = "dep1";
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
		
		if (is_numeric($on) && is_int($on)) {
			
			echo "<table>";
			
			foreach (range(1, $on) as $value) {
				echo "<tr>";
				echo "<td>" . $value . "</td>";
				echo "<td>" . "blii" . "</td>";
				echo "</tr>";
			}
			
			echo "</table>";
		}
	}
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
		\crm::Register($this, dep_test1::msg);
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
	
	}
}

?>