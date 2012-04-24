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
	public $name;

}

?>