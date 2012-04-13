<?php
namespace plugins;

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
	
	function Initialize() {
		\crm::getCurrent()->Register($this, \MESSAGES::ERROR);
		\crm::getCurrent()->Register($this, \MESSAGES::LOG);
	}
	function Plugin() {
		$handle = fopen(BASE . \CONFIGURATION::$LOGDIR . DIRECTORY_SEPARATOR . \CONFIGURATION::$STDOUT, 'w');
		
		if ($handle) {
			$this->logfile = $handle;
		}
		else {
			\crm::${\MESSAGES::ERROR}(logger::FAILED_OPEN_DIR . \CONFIGURATION::$LOGDIR, null);
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
		
		fwrite($this->logfile, $on);
	}
	function error_log($on) {
		
		error_log($on, 0);
	}
}

?>