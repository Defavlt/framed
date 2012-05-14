<?php

use factories\PDOFactory;

/** 
 * @author marcus
 * 
 */
abstract class db_object {
	
	/**
	 * The pdo-object used for db-operations
	 * @var PDO
	 */
	private $pdo;

	/**
	 * The query string.
	 * Make sure this is set before calling initialize.
	 * @var string
	 */
	private $query;
	
	/**
	 * Indicates whether we are in auto-commit-mode or not.
	 * @var unknown_type
	 */
	public $async = true;
	
	public function Initialize() {
		
		//Assert($query);
		
		$pdo = PDOFactory::get(
				CONFIGURATION::$DB_HOST,
				CONFIGURATION::$DB_PORT,
				CONFIGURATION::$DB_,
				CONFIGURATION::$DB_USER,
				CONFIGURATION::$DB_PASS);
		
		$this->pdo = $pdo;

		$this->async = 
			$this->pdo->beginTransaction();

	}
	
	public function Execute() {
		$this->obj->execute();
	}

}

?>