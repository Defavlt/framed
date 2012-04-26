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
		$params = $customer->getParamArray();
		
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

		foreach ($params as $key => $value) {
				
			$value = \crm::gGlobalParam($key);
			if (!is_null($value)) {
				
				$customer->{$key} = $value;
			}
		}
			
		if ($top != null && $top != "") {
				
			$option[$customer::OPTION_MAX_RESULTS] = $top;
		}

		$option[$customer::OPTION_CMP] = $customer::OPTION_CMP_LIKE;

		while ($customer->select($option)) {
			
			$count = 0;
			
			foreach ($params as $property) {
				
				$key = $property->name;
				if (isset($customer->{$key}) && !empty($customer->{$key})) {
					
					echo '<span style="background: #ddd;color:#222;margin: 0 2px;">' . $customer->{$key} . "</span>, ";
					$count++;
				}
			}

			if ($count > 0) {
				
				echo "<br>";
			}
		}

		echo "fields: " . $customer->fields . "<br>";
		echo "rows: " . $customer->rows . "<br>";
		
		
	}
}

?>