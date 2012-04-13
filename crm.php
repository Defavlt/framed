<?php

use interfaces\IPlugin;
use interfaces\IObservable;
use interfaces\IObserver;

/**
 *
 * @author marcus
 *        
 */
class crm implements IPlugin, IObservable {
	private static $current;
	
	private $observerlist;
	private $pluginlist;
	//private $confreader;
	private $config;
	private $get;
	private $post;  
	
	public static function getCurrent() {
		
		return crm::$current;
	} 
	
	/**
	 * Aggressively cleans the given value.
	 * @param string $value
	 * @return string
	 */
	public static function clean($value) {
		
		return htmlspecialchars(strip_tags($value));
	}
	
	public function ConfigureSettings() {
		
		/*
		CONFIGURATION::$ACTION = $this->config[CONFIGURATION::$ACTION];
		CONFIGURATION::$OBJECT = $this->config[CONFIGURATION::$OBJECT];
		CONFIGURATION::$IDENTI = $this->config[CONFIGURATION::$IDENTI];
		CONFIGURATION::$METHOD = $this->config[CONFIGURATION::$METHOD];
		CONFIGURATION::$PLUGINS = $this->config[CONFIGURATION::$PLUGINS];
		CONFIGURATION::$FALLBACK = $this->config[CONFIGURATION::$FALLBACK];
		*/

		foreach ($this->config as $key => $setting) {
			CONFIGURATION::${strtoupper($key)} = $setting;
		}
		
		if (!isset(CONFIGURATION::$EXTRA_CONF) && is_array(CONFIGURATION::$EXTRA_CONF)) {

			foreach ($this->config[CONFIGURATION::$EXTRA_CONF] as $value) {
				
				array_merge($this->config, $value);
			}
		}
	}
	
	/**
	 * Parses the available variables and fetches the appropriate action.
	 */
	function ParseVariables() {
	
		$object = $this->get[CONFIGURATION::$OBJECT];
		$action = $this->get[CONFIGURATION::$ACTION];
		$identi = $this->get[CONFIGURATION::$IDENTI];
		
		$this->SendMessage($action, $object, $identi);
	}
	
	/*
	 * Clean and set all the required variables (POST/GET).
	 */
	private function SetVariables() {
		$this->get = array();
		$this->post = array();
		
		foreach ($_GET as $key => $value) {
			
			$this->get[crm::clean($key)] = crm::clean($value);
		}
		
		foreach ($_POST as $key => $value) {
			
			$this->get[crm::clean($key)] = crm::clean($value);
		}
	}

	/**
	 * Recursively loads all the available plugins in the dir CONFIGURATION::PLUGIN_DIR
	 * @see CONFIGURATION::PLUGIN_DIR
	 */
	private function LoadPlugins($dir = null) {
		
		$dir_handle = NULL;
		$open_dir = NULL;
		
		if ($dir == null) {
			
			$dir = $this->config[CONFIGURATION::$PLUGINS];
		}
		
		else {
			
			$dir_handle = opendir($dir);
			$open_dir = readdir($dir_handle);
		}

		if (!is_array($dir)) {
			
			try {
				
				if (is_dir(BASE . $dir)) {
					
					$this->LoadPlugins($dir);
				}
				
				else {
					
					$class = str_replace(php, null, $dir);
					$instance = new $class();
					
					if ($this->RegisterPlugin ( $instance, $dir )) {
						
						$instance->Plugin();
					}
					else {
						
						unset($instance);
					}
				}
				
			} catch (Exception $e) {
				
				continue;
			}
		}
		
		else {

			foreach ($dir as $value) {
	
				try {
					
					if (is_dir(BASE . $value)) {
	
						$this->LoadPlugins($value);
					}
					else {
					
						$class = str_replace(php, null, $value);
						$instance = new $class();
						
						if ($this->RegisterPlugin ( $instance, $value )) {
							
							$instance->Plugin();
						}
						else {
							
							unset($instance);
						}
					}
	
				} catch (Exception $e) {
					
					continue;
					
				};
			}
		}
	}

	/**
	 * Registers a plugin.
	 * @see interfaces.IPlugin
	 * @param instance
	 * @return True if successfull, otherwise (if not instance of IPlugin) false
	 */
	private function RegisterPlugin($instance, $name) {
		if ($instance instanceof IPlugin) {
			
			$this->pluginlist[$name] = $instance;
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Initializes all the loaded plugins.
	 */
	private function InitializePlugins() {
		
		foreach ($this->pluginlist as $instance) {
			$instance->Initialize();
		}
	}
	
	/**
	 * @see interfaces.IObservable::SendMessage()
	 */
	function SendMessage($message, $object, $id) {
		
		if (array_key_exists($message, $this->observerlist)) {

			foreach ($this->observerlist as $key => $instance) {
				
				if ($key == $message) {
					
					$instance->Callback($object, $id);
				}
			}
		}
	}
	
	/**
	 * @see interfaces.IObservable::Register()
	 * @return boolean True if the client is successfully registered.
	 */
	function Register($client) {
		
		if ($client instanceof IObserver) {
			
			$this->observerlist[] = $client;
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * @see interfaces.IObservable::Unregister()
	 * @return boolean The number of elements unregistered, or false if no elements of $client not an instance of IObserver.
	 */
	public function Unregister($client) {
		$deleted = 0;		
		
		if ($client instanceof IObserver) {

			foreach ($this->observerlist as $key => $value) {
				
				if (($value instanceof IObserver) && $client === $value) {
					
					unset($this->observerlist[$key]);
					$deleted++;
				}
			}
			
			return $deleted;
		}
		else {
			return false;
		}
	}

	/**
	 * @see interfaces.IPlugin::Plugin()
	 */
	function Plugin () {
		crm::$current = $this;
		$this->config = parse_ini_file(CONFIG_FILE);
		$this->observerlist = array();
		$this->pluginlist = array();;
	}
	
	/**
	 * @see interfaces.IPlugin::Initialize()
	 */
	function Initialize() {
		$this->ConfigureSettings();
		$this->LoadPlugins();
		$this->InitializePlugins();
		$this->SetVariables();
		$this->ParseVariables();
	}

	/**
	 * Start the application.
	 */
	static function Start() {
		
		$_this = new crm();
		$_this->Plugin();
		$_this->Initialize();
	}
}

?>