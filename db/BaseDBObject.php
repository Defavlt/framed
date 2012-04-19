<?php
namespace db;

use factories\PDOFactory;
use interfaces\IManagedDBObject;
use exceptions\NoSuchValueException;
use exceptions\CannotBindComplexTypes;
use exceptions\ParamBindFailException;

/**
 * The base of every managed DB-object.
 * @author marcus
 *        
 */
class BaseDBObject {
	
	private $query;
	private $keyvaluetable;
	private $stmnt;

	function __construct($class) {
	
		$class = new \ReflectionClass($class);
		$constants = $class->getConstants();
	
		foreach ($constants as $key => $value) {
				
			if ($key == "query") {
	
				$this->prepare($value);
			}
			else {
				
				$this->$$key = $value;
			}
		}
	}
	
	public function prepare($query) {
		
		$this->query = $query;
		$this->stmnt = PDOFactory::prepare($query);
	}
	
	function __set($name, $args) {

		if (count($args) > 1) {
			
			throw new CannotBindComplexTypes();
		}
		else {
			
			$this->keyvaluetable[$name] = isset($args[0]) ? $args[0] : null;
			
			if (!$this->stmnt->bindParam($name, $args[0])) {
				
				throw new ParamBindFailException();
			}
		}
	}

	function __get($name) {
		
		if (key_exists($name, $this->keyvaluetable)) {
			
			return $this->keyvaluetable[$name];
		}
		
		else {
			
			throw new NoSuchValueException();
		}
	}
}

?>