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

	function gVisibility() {
		
		return PLUGIN_VISIBILITY::PU;
	}
	
	function Initialize() {
		
		\crm::gInstance()->Register($this, "test");
		\crm::log("test", IPlugin);
	}
	function Plugin() {}
	
	function Callback($on, $id, $msg) {
		
		ob_start();
		
echo <<<'EOT'

<p>This is a test plugin.</p>
<button type="submit">Press if OK</button>
EOT;

		echo ob_get_clean();

		\crm::index($on, $id);
		
	}

}

?>