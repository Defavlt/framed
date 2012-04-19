<?php
namespace interfaces;

/**
 *
 * @author marcus
 *        
 */
interface IManagedDBObject {
	
	//Implicit enforcing of private vars Done Right(r) since 1758. 
	/**
	 * Gets the PDO-object associated with this instance of IManagedDBObject.
	 * @return PDO The PDO-object.
	 */
	function getPDO();
	
	/**
	 * Gets a hashtable that represents this object.
	 */
	function getArray();
	
	/**
	 * Gets whether this instance is commiting async or not.
	 * @return bool
	 */
	function isAsync();

}

?>