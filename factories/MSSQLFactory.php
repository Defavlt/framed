<?php
namespace factories;

/**
 *
 * @author marcus
 *        
 */
class MSSQLFactory {
	
	private static $link;
	private static $host;
	private static $user;
	private static $pass;
	
	/**
	 * Singleton pattern:
	 * Don't let anything from the *outside* create a new of self.
	 */
	private function __construct() {}
	
	private static function get($host, $db, $user, $pass, $port = null) {
		
		try {
			
			if (is_null($port) || $port == "") {
				
				$link = mssql_pconnect($host, $user, $pass);
			}
			else {
				
				$link = mssql_pconnect($host . ':' . $port, $user, $pass);
			}
			
			if ($link) {
				
				self::$link = $link;

				$select_db = mssql_select_db(\CONFIGURATION::$DB);
					
				if (!$select_db) {
				
					die("Could not select db at " . \CONFIGURATION::$DB);
				}
				
			}
			else {
				die("Could not connect to db at " . $db);
			}
			
		} catch (\Exception $e) {
			
			die("Could not connect to db at " . $db);
			
		}
		
	}
	
	/**
	 * Prepares a new query for use by the connection.
	 * @param string $query
	 * @return MS SQL result resource
	 */
	public static function prepare($query) {
		
		$link = null;
		$port = null;
		
		if (!isset(self::$link)) {
			
			try {
				
				$port = \CONFIGURATION::$DB_PORT;
				
			} catch (\Exception $e) {
				
				
			}
			
			self::get(
					\CONFIGURATION::$DB_HOST,
					\CONFIGURATION::$DB,
					\CONFIGURATION::$DB_USER, 
					\CONFIGURATION::$DB_PASS,
					\CONFIGURATION::$DB_PORT
					);
		}
		
		else {
						
			$result = mssql_query($query);
			
			if ($result === FALSE) {
				
				die("Error: " . $result);
			}
			
			return $result;
		}
	}

}

?>