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
class test implements IObserver, IPlugin {
	private $visibility;
	
	function gVisibility() {
		
		return $this->visibility;
	}
	
	function Initialize() {
		
		\crm::gInstance()->Register($this, "test");
		\crm::log("Loaded plugin test", null);
		
		$this->visibility = PLUGIN_VISIBILITY::PU;
	}
	function Plugin() {}
	
	function Callback($on, $id, $msg) {
		
		ob_start();
		
echo <<<'EOT'

<p>This is a test plugin.</p>
<button type="submit">Press if OK</button>
EOT;

		echo ob_get_clean();
		
	}

}

?>