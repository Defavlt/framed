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
class sql_dbcustomer_test implements IPlugin, IObserver {
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
	public function Plugin() {}
	
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
		
		echo <<<'HTML'
<form name="customerform" action="index.php" method="get">
	<label for="o">Type</label>
	<select name="o">
		<option>name</option>
		<option>id</option>
	</select>
	
	<input type="hidden" name="a" value="dbcall"/>
	<input name="i" type="text" />
	<input type="submit" />
</form>
		
HTML;

		/**
		 * @var DBCustomer
		 */
		$customer = new DBCustomer();
		$option = array();

		if (property_exists($customer, $on)) {
			
			$customer->{$on} = $id;
			//$option[$customer::OPTION_CMP] = $customer::OPTION_CMP_EQ;
		}

		while ($customer->select($option)) {

			echo $customer->id . " : ";
			echo $customer->name . "<br>";
		}

		echo "fields: " . $customer->fields . "<br>";
		echo "rows: " . $customer->rows . "<br>";
		
		
	}
}

?>