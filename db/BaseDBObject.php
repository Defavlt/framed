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
	const SELECT_WHERE_TEMPLATE = 'WHERE %1$s';
	const SELECT_ORDER_TEMPLATE = ' ORDER BY %1$s %2$s';
	const SELECT_LIKE_TEMPLATE = '%1$s LIKE \'%2$s\' %3$s ';
	const INSERT_TEMPLATE = 'INSERT INTO %1$s(%2$s) VALUES(%3$s)';
	const UPDATE_TEMPLATE = 'UPDATE %1$s SET %2$s WHERE %3$s';
	
	static $SELECT_GROUPING_TYPE = "OR";
	
	public $fields;
	public $rows;

	private $keyvaluetable;
	private $_query;
	private $_lastresult;
	
	/**
	 * The mssql_query result resource
	 * @var MS SQL Result resource
	 */
	private $resource;
	
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
	public function select($amount = -1) {
		
		$props = $this->getParamArray();
		
		unset($props["fields"]);
		unset($props["rows"]);
		
		if (!isset($this->resource)) {
			echo "IS NOT SET";
			$class = str_replace(
					\CONFIGURATION::$DBCLASSPREFIX, 
					null, 
					get_class($this));

			$where = NULL;
			
			foreach ($props as $key => $value) {
					
				if (isset($this->$$key) && !is_null($this->$$key)) {
			
					$where .= sprintf(
							self::SELECT_LIKE_TEMPLATE,
							$key,
							$value,
							self::$SELECT_GROUPING_TYPE
					);
				}
				else {
					
					unset($props[$key]);
				}
			}
			
			substr($where, 0, (0 - strlen(self::$SELECT_GROUPING_TYPE)));
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