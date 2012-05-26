<?php
namespace db;

use interfaces\IDBExtendable;

use db\BaseDBObject;

/**
 *
 * @author marcus
 *        
 */
class DBCaseinfo extends BaseDBObject implements IDBExtendable {
	
	public $title;
	public $category;
	public $owner;
	public $firstname;
	public $lastname;
	public $phone;
	public $mobilephone;
	public $email;
	public $status;
	public $text;
	public $date;
	public $createdby;
	public $org;
}

?>