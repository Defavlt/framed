<?php
namespace plugins;

use interfaces\IObserver;
use interfaces\IPlugin;

/**
 *
 * @author marcus
 *        
 */
class sockets implements IObserver {
	const MSG_NEW_CLIENT = "MSG_NEW_CLIENT";
	const MSG_DIS_CLIENT = "MSG_DIS_CLIENT";
	const MSG_OLD_CLIENT = "MSG_OLD_CLIENT";
	
	/**
	 * Key: Port instance is listening on.
	 * 
	 * @var sockets
	 */
	private static $instances;
	private $clients;
	private $socket;
	
	private $port;
	private $ip;
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
		
		switch ($msg) {
		
		}
	}
	
	/**
	 * Creates a new object of type sockets.
	 * 
	 * @param string $ip        	
	 * @param string $port        	
	 * @return boolean True if successfully instanced, false if instance already
	 *         exists on port.
	 */
	public static function create($ip, $port) {
		
		if (! is_array ( self::$instances )) {
			
			self::$instances = array ();
		}
		
		if (! key_exists ( $ip, self::$instances )) {
			
			self::$instances [$port] = new self ( $ip, $port );
			return true;
		} else {
			
			return false;
		}
	}
	
	/**
	 * Listens on the port assigned to this instance of sockets.
	 * 
	 * @return mixed The num of clients on this object, or FALSE on disconnect.
	 */
	public function listen() {
		
		if (($client = socket_accept ( $this->socket )) !== FALSE) {

			if (!is_array($this->clients )) {
				
				$this->clients = array();
			}
			
			$this->clients[] = $client;
			return count($this->clients);

		} else {
			
			return FALSE;
		}
	}

	public function read($delimiter) {
		
		return socket_read(
				$this->socket,
				$delimiter);
	}
	
	/**
	 * Read a line from the socket bound to this instance.
	 */
	public function readline() {
		
		return socket_read(
				$this->socket, 
				NEWLINE);
	}
	
	/**
	 * Read a block (end as NEWBLOCK) from the socket bound to this instance.
	 */
	public function readblock() {
		
		return socket_read(
				$this->socket, 
				NEWBLOCK);
	}
	
	/**
	 * Sends data to the client connected to this instance.
	 * 
	 * @param mixed $data
	 * @return bool TRUE if successfully sent, false otherwise.
	 */
	public function send($data) {
		
		return socket_send(
				$this->socket, 
				$data, 
				strlen($data)) ? true : false; //Socket_send returns int or FALSE.
	}
	
	/**
	 * Accepts data from the given port
	 *
	 * @param string $port
	 * @param mixed $delimiter TIP: Use NEWBLOCK or NEWLINE.
	 * @param mixed $id
	 * @param mixed $data
	 */
	public static function accept_data($port, $delimiter, &$id, &$data) {
		
		if (key_exists($port, self::$instances)) {

			$port = self::$instances[$port]->listen();
			$data = self::$instances[$port]->read($delimiter);
			return true;
		
		}
		else {
			
			return false;
		}
	}
	
	private function __construct($ip, $port) {
		
		$this->ip = $ip;
		$this->port = $port;
		$this->socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
		
		socket_bind ( $this->socket, $ip, $port );
		socket_listen ( $this->socket );
	}
}

?>