<?php
namespace plugins;

use db\DBCaseinfo;

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
class sql_test implements IPlugin, IObserver {
	const msg = "dbcall";
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
		\crm::log ( "Trying event listener: dbcall" );
		
		if (\crm::gInstance ()->Register ( $this, self::msg )) {
			
			\crm::log ( "Event listener succeded" );
			\crm::log ( self::msg, IPlugin );
		} else {
			
			\crm::log ( "Event listener failed." );
		}
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
		
		$customer = null;
		
		if ($on == "customer") {

			/**
		 	* @var BaseDBObject
		 	*/
			$customer = new DBCustomer ();
		
		}
		else if ($on == "case") {
			
			/**
			 * 
			 * @var BaseDBObject
			 */
			$customer = new DBCaseinfo();
			
		}
		else {
			
			/**
			 * 
			 * @var BaseDBObject
			 */
			$customer = new DBCustomer();
		}
		
		$class = get_class($customer);
		$top = \crm::gGlobalParam ( "top" );
		$option = array ();
		$params = $customer->getParamArray ();
		
		echo <<<HTML
<form name="$class" action="index.php" method="get">
	<input name="get" 	type="text" placeholder="get" style="display:block;" />
	<input name="post" 	type="text" placeholder="post" style="display:block;" />
	<input name="top"   type="text" placeholder="top" value="$top" maxlength="6" style="width:60px;" style="display:block;"/>
HTML;
		
		foreach ($params as $property) {
			
			$key = $property->name;
			
			echo <<<HTML
	<input name="$key" type="text" placeholder="$key" style="display:block;" />
HTML;
		}
		
		echo <<<HTML
	<input name="a"     type="hidden" value="dbcall"/>
	<input type="submit" />
</form>
HTML;
		
		foreach ( $params as $property ) {
			$key = $property->name;
			$value = BaseDBObject::clean(\crm::gGlobalParam ( $key ));
			
			if (! is_null ( $value )) {
				
				$customer->{$key} = $value;
			}
			
			echo $key . " : " . $value . "<br>";
		}
		
		$option [$customer::OPTION_CMP] = $customer::OPTION_CMP_LIKE;
		$option [$customer::OPTION_MAX_RESULTS] = is_numeric($top) ? $top : $customer::OPTION_MAX_RESULTS;
		
		if ($customer->select ( $option )) {
			do {
				
				$count = 0;
				
				foreach ( $params as $property ) {
					
					if ($count >= 10) {
						
						$count = 0;
					}
					
					$key = $property->name;
					$colour = $count . $count . $count;
					
					if (isset ( $customer->{$key} ) && ! empty ( $customer->{$key} ) && $customer->{$key} != " ") {
						
						echo '<span style="background: #' . $colour . ';color:#FFF;margin: 0 5px;">' . $customer->{$key} . "</span>";
						$count += 8;
					}
				}
				
				if ($count > 0) {
					
					echo "<br>";
				}

			} while ( $customer->select ( $option ) );		
		}
		
		echo "fields: " . $customer->fields . "<br>";
		echo "rows: " . $customer->rows . "<br>";
	
	}
}

?>