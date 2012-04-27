<?php
namespace plugins;

use db\BaseDBObject;

use db\DBCaseinfo;

use constants\PLUGIN_VISIBILITY;

use interfaces\IObserver;
use interfaces\IPlugin;
use utils\html;

/**
 *
 * @author marcus
 *        
 */
class listplugin implements IObserver, IPlugin {
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
		
		$avail_tables = \CONFIGURATION::$TABLES;
		$class = BaseDBObject::gFullName($on);
		
		if (key_exists($on, $avail_tables) && class_exists($class)) {
			
			$this->doaction($class, $id);
		}
		else {
			
			//404?
			//crm::error_404(..);
		}
	}
	
	private function doaction($on, $id = null) {
		
		/**
		 * @var BaseDBObject
		 */
		$object = new $on();
		$params = $object->getParamArray();
		$options = $object->gQueryOptions($params);
		$order = \crm::gGlobalParam($object::CONFIG_ORDER_ITEM);
		$headers 	= null;
		$data 		= null;
		
		ob_start();
		foreach ($params as $param) {
			
			echo html::th($param);
		}
		
		$headers = ob_get_clean();

		ob_start();
		if ($object->select($options)) {
			
			do {
				
				$row_data = null;
				foreach ($params as $param) {
					
					$row_data .= html::td(
							$object->{$param->name},
							\crm::gConfig(self::TABLE_DATA_CLASS)
					);
				}
				
				echo html::tr(
					$row_data,
					array(
						"class" => \crm::gConfig(self::TABLE_ROW_CLASS)
					)
				);
				
			} while ($object->select($options));
		}
		
		$data = ob_get_clean();
		
		echo html::table(
			$data,
			\crm::gConfig(self::TABLE_CLASS)
		);
	}
	
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


	private static function gQueryOptions($params) {
		
		;
	}
}

?>