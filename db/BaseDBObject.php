<?php
namespace db;

use factories\PDOFactory;

use interfaces\IManagedDBObject;

/**
 * The base of every managed DB-object.
 * @author marcus
 *        
 */
abstract class BaseDBObject {
	
	private $query;
	private $keyvaluetable;	
	
	function setQuery($query);
	
	function __set($name, $args) {
		
		if (count($args) > 1) {
			
			$this->keyvaluetable[$name] = $args;
		}
		else {
			
			$this->keyvaluetable[$name] = isset($args[0]) ? $args[0] : null;
		}
	}
	function __get($name) {
		
		if (key_exists($name, $this->keyvaluetable)) {
			
			return $this->keyvaluetable[$name];
		}
		
		else {
			
			throw new Exception();
		}
	}
	
}

?>