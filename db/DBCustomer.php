<?php
namespace db;

use interfaces\IDBExtendable;

/**
 *
 * @author marcus
 *        
 */
class DBCustomer extends BaseDBObject implements IDBExtendable {
	
	public $id;
	public $fname;
	public $lname;
	public $addr;
	public $owner;
	
	private $query;
	
	public function getQuery() {
		
		return $this->query;
	}
	
	
}

?>