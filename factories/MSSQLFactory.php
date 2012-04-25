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
			
			if (is_null($port) || $port == "" || $port == "none") {
				
				$link = mssql_pconnect($host, $user, $pass);
			}
			else {
				
				$link = mssql_pconnect($host . ':' . $port, $user, $pass);
			}
			
			if ($link) {
				
				self::$link = $link;

				$select_db = mssql_select_db(\CONFIGURATION::$DB);
					
				if (!$select_db) {
				
					\crm::log("Could not select db at " . $db);
				}
				
			}
			else {
				\crm::log("Could not select db at " . $db);
			}
			
		} catch (\Exception $e) {
			
			\crm::log("Could not select db at " . $db);
			
		}
		
	}
	
	/**
	 * Prepares a new query for use by the connection.
	 * @param string $query
	 * @return resource
	 */
	public static function prepare($query) {
		
		$link = null;
		$port = null;
		
		if (!isset(self::$link)) {
			
			self::get(
					\CONFIGURATION::$DB_HOST,
					\CONFIGURATION::$DB,
					\CONFIGURATION::$DB_USER, 
					\CONFIGURATION::$DB_PASS,
					\CONFIGURATION::$DB_PORT
					);
		}

		$result = mssql_query($query, self::$link);
			
		if ($result === FALSE) {
				
			\crm::error("Error. #1: Query: " . $query);
			\crm::error("Error. #2: Result: " . $result);
		}
			
		return $result;
	}

}

?>