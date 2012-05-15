<?php

use plugins\sockets;

use interfaces\IObserver;
use interfaces\IPlugin;

/**
 *
 * @author marcus
 *        
 */
class server implements IObserver, IPlugin {
	const MSG_LISTEN	= "MSG_LISTEN";
	
	const MSG_INCOMING  		= "MSG_INCOMING";
	const MSG_INCOMING_CON		= "MSG_INCOMING_CON";
	const MSG_INCOMING_MSG		= "MSG_INCOMING_MSG";
	
	const CONF_PORT				= "server.port";
	const CONF_IP				= "server.ip";
	
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
		
		switch ($msg) {
			case self::MSG_INCOMING:
				$this->incoming($on, $id);
				break;
			case self::MSG_LISTEN:
				$this->start();
				break;
		};
	}
	
	private function incoming($type, $data) {
		
		switch ($type) {
			
			case self::MSG_INCOMING_CON:
				break;
			case self::MSG_INCOMING_MSG:
				break;
		};
	}
	
	private function start() {
		
		;
	}

	private function listen($ip = null, $port = null) {
		
		if ($ip == null) {
			
			$ip = \crm::gConfig(self::CONF_IP);
		}
		
		if ($port == null) {
			
			$port = \crm::gConfig(self::CONF_PORT);
		}
		
		return sockets::create($ip, $port);
	}
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
	
	}
	
	/**
	 *
	 * @see IPlugin::Plugin()
	 *
	 */
	public function Plugin() {
	
	}
	
	/**
	 *
	 * @return PLUGIN_VISIBILITY
	 *
	 * @see IPlugin::gVisibility()
	 *
	 */
	public function gVisibility() {
	
	}
}

?>