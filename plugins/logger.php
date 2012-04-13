<?php
namespace plugins;

use settings\PLUGIN_VISIBILITY;
use interfaces\IObserver;
use interfaces\IPlugin;

/**
 *
 * @author marcus
 *        
 */
class logger implements IPlugin, IObserver {
	const FAILED_OPEN_DIR = "Failed to open log directory: ";
	private $logfile;
	
	private $visibility;
	
	function gVisibility() {
		return $this->visibility;
	}
	
	function Initialize() {
		\crm::gInstance()->Register($this, \MESSAGES::ERROR);
		\crm::gInstance()->Register($this, \MESSAGES::LOG);
		
		$this->visibility = PLUGIN_VISIBILITY::PR;
	}
	function Plugin() {
		$handle = fopen(BASE . \CONFIGURATION::$LOGDIR . DIRECTORY_SEPARATOR . \CONFIGURATION::$STDOUT, 'w') or die("Can't open logfile");
		
		if ($handle !== false) {
			$this->logfile = $handle;
		}
		else {
			\crm::error(logger::FAILED_OPEN_DIR . BASE . \CONFIGURATION::$LOGDIR . DIRECTORY_SEPARATOR . \CONFIGURATION::$STDOUT, null);
		}
	}
	
	function Callback($on, $id, $msg) {
		
		switch ($msg) {
			
			case \MESSAGES::LOG:
				$this->log($on);
				break;
			case \MESSAGES::ERROR:
				$this->error_log($on);
				break;
			default:
				break;
		}
	}

	function log($on) {
		
		
		$message = "[D M d h:i:s Y] [log]";
		fwrite( $this->logfile, date($message) . $on);
	}
	function error_log($on) {
		
		error_log($on, 0);
	}
}

?>