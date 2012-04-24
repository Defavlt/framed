<?php
namespace plugins;

use db\DBCustomer;
use constants\PLUGIN_VISIBILITY;
use interfaces\IPlugin;
use interfaces\IObserver;
use db\BaseDBObject;

/**
 *
 * @author marcus
 *        
 */
class sql_dbcustomer_test extends BaseDBObject implements IPlugin, IObserver {
	const msg = "dbcall";
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
		\crm::log("Trying event listener: dbcall");
		
		if (\crm::gInstance()->Register($this, self::msg)) {
			
			\crm::log("Event listener succeded");
			\crm::log(self::msg, IPlugin);
		}
		else {
			
			\crm::log("Event listener failed.");
		}
	}
	
	/**
	 *
	 * @see IPlugin::Plugin()
	 *
	 */
	public function Plugin() {
		echo $this instanceof IPlugin . "<br>";
		echo $this instanceof BaseDBObject . "<br>";
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
		
		$customer = new DBCustomer();
		
		while ($customer->select()) {
			
			echo $customer->id . "<br>";
			echo $customer->fname . "<br><br>";
		}
	}
}

?>