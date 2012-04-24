<?php
namespace plugins;

use constants\PLUGIN_VISIBILITY;
use constants\MESSAGES;
use interfaces\IObserver;
use interfaces\IPlugin;

/**
 * Class needed for logging operations.
 * To remove it (without breaking dependencies), *.ini-file.
 * @author marcus
 *        
 */
class logger implements IPlugin, IObserver {
	const FAILED_OPEN_DIR = "Failed to open log directory: ";
	private $logfile;
	
	function gVisibility() {
		return PLUGIN_VISIBILITY::PR;
	}
	
	function Initialize() {
	}
	function Plugin() {
		$handle = fopen(BASE . \CONFIGURATION::$LOGDIR . DIRECTORY_SEPARATOR . \CONFIGURATION::$STDOUT, 'a') or die("Can't open logfile");
		
		if ($handle !== false) {
			$this->logfile = $handle;
		}
		else {
			\crm::error(logger::FAILED_OPEN_DIR . BASE . \CONFIGURATION::$LOGDIR . DIRECTORY_SEPARATOR . \CONFIGURATION::$STDOUT, null);
		}
		
		\crm::gInstance()->Register($this, MESSAGES::ERROR);
		\crm::gInstance()->Register($this, MESSAGES::LOG);
		\crm::log("New session");
	}
	
	function Callback($on, $id, $msg) {
		
		switch ($msg) {
			
			case MESSAGES::LOG:
				$this->log($on, $id);
				break;
			case MESSAGES::ERROR:
				$this->error_log($on);
				break;
			default:
				break;
		}
	}

	function log($on, $id) {

		$datef = "[D M d h:i:s Y] ";
		$message = null;
		
		switch ($id) {
			case IPlugin:
				$message .= "Loaded plugin: " . $on;
				break;

			default:
				$message .= $on;
				break;
		}
		
		fwrite( $this->logfile, date($datef) . $message . "\n");
	}
	function error_log($on) {
		
		error_log($on, 0);
	}
}

?>