<?php

use interfaces\IObserver;
use interfaces\IPlugin;

/**
 *
 * @author marcus
 *        
 */
class server implements IObserver, IPlugin {
	const MSG_INTERRUPT = 0x10;
	const MSG_CLIENT	= 0x20;
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
		
		switch ($msg) {
			case self::MSG_INTERRUPT:
				break;
			case self::MSG_CLIENT:
				break;
			case self::MSG_LISTEN:
				$this->listen();
				break;
		}
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