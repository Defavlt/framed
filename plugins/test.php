<?php
namespace plugins;

use interfaces\IObserver;
use interfaces\IPlugin;

/**
 *
 * @author marcus
 *        
 */
class test implements IObserver, IPlugin {
	
	function Initialize() {
		
		\crm::getCurrent()->Register($this, "test");
	}
	function Plugin() {}
	
	function Callback($on, $id) {
		
		ob_start();
		
echo <<<'EOT'

<p>This is a test plugin.</p>
<button type="submit">Press if OK</button>
EOT;

		echo ob_get_clean();
		
	}

}

?>