<?php
namespace db;

use factories\MSSQLFactory;

use interfaces\IDBExtendable;

use factories\PDOFactory;
use interfaces\IManagedDBObject;
use exceptions\NoSuchValueException;
use exceptions\CannotBindComplexTypes;
use exceptions\ParamBindFailException;
use exceptions\NotInstanceOfException;
use \PDO;

/**
 * The base of every managed DB-object.
 * @author marcus
 *        
 */
abstract class BaseDBObject {
	const PARAM_PREFIX = '?';
	const SELECT_ALL = 0x01;
	const SELECT_ORDER = 0x02;
	const SELECT_WHERE = 0x03;
	const SELECT_DEF_AMOUNT = 0x20;
	const SELECT_ORDER_DESC = "DESC";
	const SELECT_ORDER_ASC  = "ASC";
	const SELECT_TEMPLATE = 'SELECT * FROM %1$s %2$s';
	const SELECT_TOP_TEMPLATE = 'SELECT TOP(%1$s) * FROM %2$s %3$s';
	const SELECT_WHERE_TEMPLATE = ' WHERE %1$s';
	const SELECT_ORDER_TEMPLATE = ' ORDER BY %1$s %2$s';
	const SELECT_LIKE_TEMPLATE = '%1$s LIKE \'\%%2$s\%\' %3$s ';
	const INSERT_TEMPLATE = 'INSERT INTO %1$s(%2$s) VALUES(%3$s)';
	const UPDATE_TEMPLATE = 'UPDATE %1$s SET %2$s WHERE %3$s';
	
	const SELECT_GROUPING_TYPE_OR = "OR";
	const SELECT_GROUPING_TYPE_AND = "AND";
	
	public $fields;
	public $rows;

	private $keyvaluetable;
	private $_query;
	private $_lastresult;
	
	/**
	 * The mssql_query result resource
	 * @var resource
	 */
	private $resource;
	
	private static function name() {
		
		$name = array_slice(explode('\\', get_class($this)), -1);
		return $name[0];
	} 
	
	/**
	 * Gets an array with the public params in $this.
	 * @return Array
	 */
	function getParamArray() {

		$refclass = new \ReflectionClass($this);
		$props = $refclass->getProperties(\ReflectionProperty::IS_PUBLIC);
		
		return $props;
	}

	/**
	 * @param IDBExtandable $instance
	 */
	function __construct(array $params= null) {

		if (!($this instanceof \interfaces\IDBExtendable)) {
	
			throw new NotInstanceOfException(IDBExtendable);
		}
		else {
			
			if (is_array($params)) {

				foreach ($params as $key => $value) {
					
					if (isset($this->$$key)) {
						
						$this->$$key = $value;
					}
				}
			}
		}
	}

	/**
	 * Executes a SELECT query based on the current instance of IDBExtandable.
	 * @param int $amount The top amount of results to return.
	 * @return boolean TRUE if the query resulted in a result 
	 * (and subsequently the overlaying $this got populated), FALSE otherwise.
	 * 
	 * @example
	 * class DBCustomer {
	 *     public $name;
	 *     public $id;
	 * }
	 * $customer = new $DBCustomer();
	 * while($customer->select()) {
	 * 		echo $customer->name;
	 *  	ehco $customer->id;
	 * }
	 */
	public function select($amount = -1, int $option = null) {
		
		$props = $this->getParamArray();
		$class = self::name();
		
		unset($props["fields"]);
		unset($props["rows"]);
		
		var_dump($this->resource);
		
		if (!isset($this->resource) || $this->resource == null) {

			$table = strtolower(str_replace(
					\CONFIGURATION::$DBCLASSPREFIX, 
					null, 
					$class));

			$where = NULL;
			
			foreach ($props as $value) {
				
				if ($value->class == $class) {
					$key = $value->name;
	
					$where .= sprintf(
							self::SELECT_LIKE_TEMPLATE,
							$key,
							isset($this->$$key) ? $value->getValue($this) : "",
							self::SELECT_GROUPING_TYPE
					);
				}
				else {
					
					continue;
				}
			}
			
			$where = substr($where, 0, (0 - strlen(self::SELECT_GROUPING_TYPE)));
			
			$where = sprintf(
					self::SELECT_WHERE_TEMPLATE,
					$where
					);
			
			if($amount > -1) {
				
				$query = sprintf(
						self::SELECT_TOP_TEMPLATE,
						$amount,
						$table,
						$where
				);
			}
			else {
				
				$query = sprintf(
						self::SELECT_TEMPLATE,
						$table,
						$where
				);
			}
			
			var_dump($props);
			$this->_query = $query;
			$this->resource = MSSQLFactory::prepare($this->_query);
			
		}
		
		if (($this->rows = mssql_num_rows($this->resource) > 0)) {
		
			$this->fields = mssql_num_fields($this->resource);
			$result = mssql_fetch_assoc($this->resource);
			$this->_lastresult = $result;
			
			foreach ($result as $key => $value) {
				
				$this->$$key = $value;
			}
			
			return true;
		
		}
		else {
			
			mssql_free_result($this->resource);
			unset($this->resource);
			return false;
		}
	}
	
}

?>