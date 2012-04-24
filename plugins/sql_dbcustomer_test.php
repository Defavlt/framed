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
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
		\crm::gInstance()->Register($this, "dbcall");
		\crm::log("dbcall", IPlugin);
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