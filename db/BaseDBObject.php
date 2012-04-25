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
	const OPTION_CMP = 0x1;
	const OPTION_CMP_EQ = "=";
	const OPTION_CMP_LIKE = "LIKE";

	const OPTION_GROUPING = 0x2;
	const OPTION_GROUPING_OR = "OR";
	const OPTION_GROUPING_AND = "AND";
	
	const OPTION_ORDER = 0x3;
	const OPTION_ORDER_DESC = "DESC";
	const OPTION_ORDER_ASC = "ASC";
	
	const OPTION_MAX_RESULTS = 0x4;

	const SELECT_ORDER_DESC = "DESC";
	const SELECT_ORDER_ASC  = "ASC";
	const SELECT_TEMPLATE = 'SELECT %3$s FROM %1$s %2$s';
	const SELECT_TOP_TEMPLATE = 'SELECT TOP(%1$s) %4$s FROM %2$s %3$s';
	const SELECT_WHERE_TEMPLATE = ' WHERE %1$s';
	const SELECT_ORDER_TEMPLATE = ' ORDER BY %1$s %2$s';
	const SELECT_LIKE_TEMPLATE = '%1$s LIKE %2$s %3$s ';
	const SELECT_EQ_TEMPLATE = '%1$s = %2$s %3$s ';
	const INSERT_TEMPLATE = 'INSERT INTO %1$s(%2$s) VALUES(%3$s)';
	const UPDATE_TEMPLATE = 'UPDATE %1$s SET %2$s WHERE %3$s';
	const PARAM_GROUP = ' %1$s, ';
	
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
	
	/**
	 * Fetch the 'Name'-part of any class-name.
	 * @param string $name
	 */
	private static function name($name) {
		
		$single_slash = <<<'HTML'
\
HTML;

		$name = explode($single_slash, $name);
		return array_pop($name);
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
	 * @param array $option An array containing 
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
	public function select($amount = -1, array $option = null) {

		$props = $this->getParamArray();
		$class = get_class($this);
		
		$option = is_array($option) ?
			$option :
			array();
		
		//
		// Initialize Options
		$option[self::OPTION_CMP] = isset($option[self::OPTION_CMP]) ?
			$option[self::OPTION_CMP] :
			self::OPTION_CMP_LIKE;
		
		$option[self::OPTION_GROUPING] = isset($option[self::OPTION_GROUPING]) ?
			$option[self::OPTION_GROUPING] :
			self::OPTION_GROUPING_OR;
		
		$option[self::OPTION_MAX_RESULTS] = isset($option[self::OPTION_MAX_RESULTS]) ?
			$option[self::OPTION_MAX_RESULTS] :
				isset(\CONFIGURATION::$DB_MAX_RESULTS) ?
					\CONFIGURATION::$DB_MAX_RESULTS :
					self::OPTION_MAX_RESULTS;
		
		// Initialize Options
		//
		
		unset($props["fields"]);
		unset($props["rows"]);
		
		if (!isset($this->resource) || $this->resource == null) {

			$where 	= NULL;
			$params = NULL;
			$table 	= str_replace(\CONFIGURATION::$DBCLASSPREFIX, null, self::name($class));
			$table 	= strtolower($table);
			
			foreach ($props as $value) {
				
				if ($value->class == $class) {
					$key = $value->name;
	
					if (isset($this->$$key)) {
						
						$where .= sprintf(
								self::SELECT_EQ_TEMPLATE,
								$key,
								(isset($this->$$key) ? $value->getValue($this) : ""),
								$option[self::OPTION_GROUPING]
								);
					}
					else {
						$where .= sprintf(
							self::SELECT_LIKE_TEMPLATE,
							$key,
							"'%" . (isset($this->$$key) ? $value->getValue($this) : "") . "%'",
							$option[self::OPTION_GROUPING]
						);
					}
					
					$params .= sprintf(
							self::PARAM_GROUP,
							$key
							);
				}
				else {
					
					continue;
				}
			}
			
			$where = substr($where, 0, -3);
			$params = substr($params, 0, -2) . " ";
			
			$where = sprintf(
					self::SELECT_WHERE_TEMPLATE,
					$where
			);

			$query = sprintf(
				self::SELECT_TOP_TEMPLATE,
				$amount == -1 ? 
					\CONFIGURATION::$DB_MAX_RESULTS : 
					$amount,
				$table,
				$where,
				$params
			);

			$this->_query = $query;
			$this->resource = MSSQLFactory::prepare($this->_query);
			$this->rows = mssql_num_rows($this->resource);
			
			if($this->rows < 1) {
				
				return false;
			}
			
		}

		$this->fields = mssql_num_fields($this->resource);
		$result = mssql_fetch_assoc($this->resource);
		$this->_lastresult = $result;
		
		if ($this->_lastresult === FALSE) {
			
			return false;
		}
		else {

			foreach ($result as $key => $value) {
			
				if (isset($this->$$key) || property_exists($this, $key)) {

					$this->{$key} = $value;
				}
			}
			
			return true;
		}

	}
	
}

?>