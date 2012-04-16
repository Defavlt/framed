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
	
	function gVisibility() {
		return PLUGIN_VISIBILITY::PR;
	}
	
	function Initialize() {
		\crm::gInstance()->Register($this, \MESSAGES::ERROR);
		\crm::gInstance()->Register($this, \MESSAGES::LOG);
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
				$this->log($on, $id);
				break;
			case \MESSAGES::ERROR:
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