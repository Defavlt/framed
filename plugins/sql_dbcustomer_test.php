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
		
		/**
		 * @var DBCustomer
		 */
		$customer = new DBCustomer();
		$top = \crm::gGlobalParam("top");
		$option = array();
		
		echo <<<HTML
<form name="customerform" action="index.php" method="get">
	<select name="o">
		<option>name</option>
		<option>id</option>
		<option>all</option>
	</select>
	
	<input type="hidden" name="a" value="dbcall"/>
	<input name="i" type="text" placeholder="search" value="$id" />
	<input type="text" name="top" placeholder="top" value="$top" maxlength="6" style="width:60px;" />
	<input type="submit" />
</form>
		
HTML;

		if (property_exists($customer, $on)) {
			
			$customer->{$on} = $id;
			//$option[$customer::OPTION_CMP] = $customer::OPTION_CMP_EQ;
		}
		else if ($on == "all") {
			
			$params = $customer->getParamArray();
			
			foreach ($params as $key => $value) {
				
				$customer->{$key} = $value;
			}
			
			$option[$customer::OPTION_CMP] = $customer::OPTION_CMP_LIKE;
		}

		if (!is_null($top)) {
			
			$option[$customer::OPTION_MAX_RESULTS] = $top;
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