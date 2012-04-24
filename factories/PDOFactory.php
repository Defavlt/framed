<?php
namespace factories;

/**
 * Creates a new PDO-object for use in connecting to db.
 * @author marcus
 *
 */
class PDOFactory {

	const _ms_driver	= 'odbc:Driver=FreeTDS;';
	const _ms_host		= 'host=%s';
	const _ms_db		= 'dbname=%s';
	const _ms_port		= 'port=%s;';
	const _ms_user		= ', %s';
	const _ms_pass		= ', %s;';
	
	private static $pdo;

	/**
	 * Singleton pattern:
	 * Don't let anything from the *outside* create a new of self.
	 */
	private function __construct() {}
	
	/**
	 * Gets a new PDO-object
	 * @param string $hostname The hostname or ip to the server
	 * @param string $port The port to use when connecting.
	 * @param string $PDOFactory The PDOFactory to connect to.
	 * @param string $username The username to use when connecting.
	 * @param string $password The password to use when connecting.
	 * @return \PDO The database-object.
	 */
	private static function get($hostname, $port, $database, $username, $password) {

		try {

			$pdo = new PDO(
					self::ms_driver() 		 	.
					self::ms_host($hostname) 	.
					self::ms_port($port) 	 	.
					self::ms_db($database)	 	.
					self::ms_user($username) 	.
					self::ms_pass($password));
			
			//Assert($pdo);

			if (!$pdo) {
				
				throw new Exception();
			}
			
			return $pdo;

		}

		catch (PDOException $e) {

			die(self::ERROR_NO_CONNECTION($database));
		}
	}

	/**
	 * Builds a string
	 * @param string $name The name of the function.
	 * @param array $args 0x0 should always correspond to the data.
	 */
	private static function __callstatic($name, $args) {
		
		$prefix = '_';
		$pattern = '/^ms_\w';
		
		if (preg_match($pattern, $name)) {
			
			return sprintf(
				self::${$prefix . $name},
				$args[0]);
		}
		
		else {
			
			if (method_exists(self, $name)) {
				
				self::$name($args);
			}
			
			else if (method_exists(self::$pdo, $name)) {
				
				$func = array(
						self::$pdo,
						$name
						);

				call_user_func($func);
			}
		}
	}
	
	/**
	 * Prepares a query and returns a statement object.
	 * @param string $query
	 */
	public static function prepare($query) {
		
		$pdo = null;
		
		if (!isset(self::$pdo)) {

			$pdo = self::get(
						\CONFIGURATION::$DB_HOST,
						\CONFIGURATION::$DB_PORT,
						\CONFIGURATION::$DB_,
						\CONFIGURATION::$DB_USER,
						\CONFIGURATION::$DB_PASS);
		}
		
		self::$pdo = $pdo;
		self::$pdo->beginTransaction();
		
		
		return self::$pdo->prepare($query);
	}
}

?>