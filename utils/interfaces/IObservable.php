<?php
namespace interfaces;

/**
 * Makes an object eligible for observing.
 * @author marcus
 *        
 */
interface IObservable {
	
	/**
	 * Sends a message to all registered clients.
	 * @param string $message
	 * @param mixed $object
	 * @param string $id
	 * @return boolean false if no object is listening.
	 */
	function SendMessage($message, $object, $id, $from_public = false);
	
	/**
	 * Registers the client with this instance
	 * @param IObserver $client
	 */
	function Register($client, $msg);
	
	/**
	 * Unregisters the client from this instance
	 * @param IObserver $client
	 */
	function Unregister($client, $msg);
}

?>