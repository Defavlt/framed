<?php
namespace db;

use factories\MSSQLFactory;
use interfaces\IDBExtendable;
use interfaces\IManagedDBObject;
use exceptions\NoSuchValueException;
use exceptions\CannotBindComplexTypes;
use exceptions\ParamBindFailException;
use exceptions\NotInstanceOfException;

/**
 * The base of every managed DB-object.
 * @author marcus
 *        
 */
abstract class BaseDBObject {
	const OPTION_CMP 			= 0x10;
	const OPTION_CMP_EQ 		= "=";
	const OPTION_CMP_LIKE 		= "LIKE";

	const OPTION_GROUPING 		= 0x20;
	const OPTION_GROUPING_OR 	= "OR";
	const OPTION_GROUPING_AND 	= "AND";
	
	const OPTION_ORDER 			= 0x30;
	const OPTION_ORDER_ITEM		= 0x31;
	const OPTION_ORDER_DESC 	= "DESC";
	const OPTION_ORDER_ASC 		= "ASC";
	
	const OPTION_MAX_RESULTS 	= 0x40;

	const SELECT_TEMPLATE = 'SELECT TOP(%1$s) %2$s FROM %3$s %4$s %5$s';

	const INSERT_TEMPLATE = 'INSERT INTO %1$s(%2$s) VALUES(%3$s)';
	const UPDATE_TEMPLATE = 'UPDATE %1$s SET %2$s WHERE %3$s';

	const PARAM_GROUP = ' %1$s,';
	const PARAM_CMP = '%1$s %2$s %3$s %4$s ';
	
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
					
					if (isset($this->{$key})) {
						
						$this->{$key} = $value;
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
	public function select(array $option = null) {

		$props = $this->getParamArray();
		$class = get_class($this);
		$order = null;
		
		unset($props["fields"]);
		unset($props["rows"]);
		
		if (!isset($this->resource) || $this->resource == null) {

			$where 	= "WHERE ";
			$params = NULL;
			$table 	= str_replace(\CONFIGURATION::$DBCLASSPREFIX, null, self::name($class));
			$table 	= strtolower($table);
			$cmp 	= $option[self::OPTION_CMP];
			
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
			
			$option[self::OPTION_MAX_RESULTS] = (isset($option[self::OPTION_MAX_RESULTS]) ?
				$option[self::OPTION_MAX_RESULTS] :
				(isset(\CONFIGURATION::$DB_MAX_RESULTS) ?
					\CONFIGURATION::$DB_MAX_RESULTS :
					self::OPTION_MAX_RESULTS));
			
			if (isset($option[self::OPTION_ORDER_ITEM])) {
					
				$option[self::OPTION_ORDER] = isset($option[self::OPTION_ORDER]) ?
					$option[self::OPTION_ORDER] :
					self::OPTION_ORDER_DESC;
			
				$order  = "ORDER BY ";
				$order .= $option[self::OPTION_ORDER_ITEM] . " ";
				$order .= $option[self::OPTION_ORDER];
			}
			
			// Initialize Options
			//
			
			foreach ($props as $prop) {
				
				if ($prop->class == $class) {
					$key = $prop->name;

					$value 	= $this->{$key};
					$value 	= $cmp == self::OPTION_CMP_EQ ?
						"'"  . $value . "'" :
						"'%" . $value . "%'";
						
					if (isset($this->{$key}) && $this->{$key} != "" && $this->{$key} != " ") {
						$where .= sprintf(
								self::PARAM_CMP,
								$key,
								$cmp,
								$value,
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
			
			if ($this->endswith($where, "WHERE ")) {
				
				$where = null;
			}
			else if ($this->endswith($where, $option[self::OPTION_GROUPING] . " ")) {
				
				$where = substr($where, 0, -strlen($option[self::OPTION_GROUPING] . " "));
			}

			if ($this->endswith($params, ",")) {
				
				$params = substr($params, 0, -1) . " ";
			}

			$query = sprintf(
				self::SELECT_TEMPLATE,
				$option[self::OPTION_MAX_RESULTS],
				$params,
				$table,
				$where,
				$order
			);

			$this->_query = $query;
			$this->resource = MSSQLFactory::prepare($this->_query);
			
			echo $this->_query . "<br>";
			
			if ($this->resource !== FALSE && $this->resource !== null) {

				$this->rows = mssql_num_rows($this->resource);

				if($this->rows < 1) {

					return false;
				}
			}
			else {

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

				if (isset($this->{$key}) || property_exists($this, $key)) {

					$this->{$key} = $value;
				}
			}
			
			return true;
		}

	}
	
	/**
	 * Determines whether the whole string ends with the end string.
	 * @param string $whole
	 * @param string $end
	 * @return boolean True if it ends with $end, false otherwise.
	 */
	private function endswith($whole, $end)
	{
	    return (strpos($whole, $end, strlen($whole) - strlen($end)) !== false);
	}
	
	/**
	 * Sanitizes db input
	 * @param mixed $param
	 */
	public static function clean($param) {
		
		if (is_array($param)) {
			$return = array();

			foreach ($param as $key => $value) {
				
				$return[self::clean($key)] = self::clean($value);
			}
			
			return $return;
		}
		else {
			
			$param = htmlentities($param, ENT_QUOTES|ENT_SUBSTITUTE	);
			$single_slash = <<<'HTML'
\
HTML;

			$pattern = <<<'HTML'
([-])
HTML;
			
			$replacement = 
				"'$0";
			
			$return = preg_replace($pattern, $replacement, $param);
			
			return $return;
		}
	}

	/**
	 * Unsanitizes db input
	 * @param mixed $param
	 */
	public static function unclean($param) {
		
		if (is_array($param)) {
			$return = array();
			
			foreach ($param as $key => $value) {
				
				$return[self::unclean($key)] = self::unclean($value);
			}
			
			return $return;
		}
		else {
			
			$single_slash = <<<'HTML'
\
HTML;
			
			$pattern =
				"'(['%$&`´-@$single_slash])";
			
			$replacement = 
				"$0";

			$return = preg_filter($pattern, $replacement, $param);
			
			return $return;
		}
	}
}

?>