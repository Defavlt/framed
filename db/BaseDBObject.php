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
	const SELECT_ORDER = "DESC";
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
	 * @var \PDOStatement
	 */
	private $stmnt;
	
	/**
	 * The mssql_query result resource
	 * @var MS SQL Result resource
	 */
	private $resource;

	function __set($name, $arg) {

		$this->keyvaluetable[ self::PARAM_PREFIX . $name] = $arg;
		//$this->stmnt->bindParam($prefix . $name, $arg);
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
	 * Executes the statement and prepares for fetching of resources.
	 */
	function execute() {

		$this->stmnt->execute();
	}

	/**
	 * Fetch the next row in the initialized statement.
	 * @param constant int $fetch_style
	 */
	public function selectNext($fetch_style = NULL) {
		
		if (($row = $this->stmnt->fetch($fetch_style)) != null) {
			$props = $this->getParamArray();
			
			foreach ($props as $key => $value) {
				
				$this->$$key = $row->$$key;
			}
			
			return true;
		}
		else {
			return false;
		}
	}

	public function update() {
		
		throw new \Exception("Not implemented yet.");
	}

	public function insert() {
		
		$props = $this->getParamArray();
		$name = get_class($this);
		$params = null;
		$pl_params = null;
		$names = null;
		
		foreach ($props as $key => $value) {
			
			$params .= $key . ',';
			$pl_params .= ':' . $key . ',';
			$names .= $key;
		}
		
		$params = substr($params, 0, -1) . " ";
		$pl_params = substr($pl_params, 0, -1) . " ";
		$query = sprintf(
				self::INSERT_TEMPLATE,
				$name,
				$params,
				$pl_params);
		
		$this->stmnt = PDOFactory::prepare($query);
		
		//No, this loop is *NOT* redundant.
		//PDO::Prepare() must be invoked BEFORE bindParam
		//(or, in other words: $this->stmnt must be populated) 
		foreach ($props as $key => $value) {
			
			$this->stmnt->bindParam(':' . $key, $this->$$key);;
		}
		
		$this->stmnt->execute();
	}

	/*-----------------------------*/
	
	/**
	 * @param IDBExtandable $instance
	 */
	function __construct() {
	
		if (!$this instanceof IDBExtendable) {
	
			throw new NotInstanceOfException(IDBExtendable);
		}
	}

	public function __set($name, $arg) {

		$this->keyvaluetable[$name] = $arg;
	}

	public function select(int $amount = -1, $option = null) {
		
		$props = $this->getParamArray();
		
		unset($props["fields"]);
		unset($props["rows"]);
		
		if (!isset($this->resource)) {
			
			$class = get_class($this);
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