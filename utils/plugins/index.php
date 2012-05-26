<?php
namespace plugins;

use interfaces\IPlugin;
use interfaces\IObserver;
use constants\PLUGIN_VISIBILITY;

/**
 *
 * @author marcus
 *        
 */
class index implements IPlugin, IObserver {
	
	/**
	 *
	 * @see IPlugin::Initialize()
	 *
	 */
	public function Initialize() {
		\crm::gInstance()->Register($this, "index");
		\crm::log("index", IPlugin);
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
		return PLUGIN_VISIBILITY::PU;
	}
	
	/**
	 *
	 * @see IObserver::Callback()
	 *
	 */
	public function Callback($on, $id, $msg) {
		
		$msgs = \crm::gInstance()->observerlist;
		$a = \CONFIGURATION::$ACTION;
		$o = \CONFIGURATION::$OBJECT;
		$i = \CONFIGURATION::$IDENTI;
		
		echo <<<HTML
<form name="index" action="index.php" method="get">
	<label for="$a">Msg: </label>
	<select name="$a">		
HTML;
		
		foreach ($msgs as $key => $value) {
			
			echo <<<HTML
		<option>$key</option>
HTML;

		}
		
		echo <<<HTML
	</select>
	<input name="$o" type="text" placeholder="Data: $o" style="display:block;"/>
	<input name="$i" type="text" placeholder="Data: $i" style="display:block;"/>
</form>
HTML;
		echo "index, default page.";
	}
}

?>