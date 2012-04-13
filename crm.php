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
	
	/**
	 * Gets the current instance object of crm.
	 * @return crm
	 */
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
			
			if (property_exists(CONFIGURATION, $key)) {
				CONFIGURATION::${strtoupper($key)} = $setting;
			}
			else {
				continue;
			}
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
	private function LoadPlugins() {
		
		var_dump(CONFIGURATION::$PLUGINS);
		if (isset(CONFIGURATION::$PLUGINS) && is_array(CONFIGURATION::$PLUGINS)) {
		
			foreach (CONFIGURATION::$PLUGINS as $plugin) {
				try {
					
					$single_slash = <<<'EOT'
\
EOT;
				
					$class = BASE . CONFIGURATION::$PLUGIN_DIR . $single_slash . str_replace(php, null, $plugin);
					$instance = new $class();
				
					if ($this->RegisterPlugin($instance, $plugin)) {
							
						$instance->Plugin();
					}
				
					else {
							
						unset($instance);
					}
				
				} catch (Exception $e) {
					continue;
				}
			}
			
			$this->log("Plugins finished loading", null);
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
		
		error_log("BEFORE CHECK", 0);
		error_log($message, 0);
		error_log($object, 0);
		var_dump($this->observerlist);
		if (array_key_exists($message, $this->observerlist)) {
			error_log("BEFORE LOOP", 0);
			foreach ($this->observerlist as $msg => $objects) {
				error_log("INSIDE LOOP", 0);
				if ($msg == $message) {
					error_log("BEFORE LISTENER CHECK", 0);
					foreach ($objects as $instance) {

						/**
						 * The return type of the message handler.
						 * @var MESSAGE_RETURN_TYPE
						 */
						$return = $instance->Callback($object, $id, $msg);
						
						switch ($return) {
							case MESSAGE_RETURN_TYPE::NOT_PUBLIC:
								break;
							case MESSAGE_RETURN_TYPE::STOP_CHAIN:
								break 2; //Stop the chain and return to calle�

							default:
								continue;
						}
					}
				}
			}
		}
	}
	
	/**
	 * @see interfaces.IObservable::Register()
	 * @return boolean True if the client is successfully registered.
	 */
	function Register($client, $msg) {
		
		if ($client instanceof IObserver) {
			
			if (isset($this->observerlist[$msg]) && is_array($this->observerlist[$msg])) {
				
				$this->observerlist[$msg][] = $client;
				return true;
			}
			else {
				
				$this->observerlist[$msg] = array();
				$this->observerlist[$msg][] = $client;
				return true;
			}
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
				
				if (($key instanceof IObserver) && $client === $key) {
					
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
		
		print_r($this);
	}

	/**
	 * Dynamically invokes a message whenever a method that doesn't exist is invoked.
	 * @param string $name 
	 * @param array $args 0: MESSAGE_ARG_TYPE::ON, 1: MESSAGE_ARG_TYPE::ID
	 */
	public static function __callStatic($name, $args) {
		
		$func = array();
		
		if (method_exists('crm', $name)) {
			
			$func[] = 'crm';
			$func[] = $name;
			
			call_user_func($func);
		}
		else {
			
			\crm::getCurrent()->SendMessage($name, $args[MESSAGE_ARG_TYPE::ON], $args[MESSAGE_ARG_TYPE::ID]);
		}
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