<?php

use interfaces\IPlugin;
use interfaces\IObservable;
use interfaces\IObserver;
use settings\PLUGIN_VISIBILITY;

/**
 *
 * @author marcus
 *        
 */
class crm implements IPlugin, IObservable {
	private static $current;
	
	private $observerlist;
	private $pluginlist;
	private $config;
	private $get;
	private $post;
	
	/**
	 * Gets a config-value
	 * 
	 * @param string $param
	 *        	The key of the value
	 */
	public static function gConfig($param) {
		
		if (key_exists ( $param, crm::gInstance ()->config )) {
			
			return crm::gInstance ()->config [$param];
		} else {
			return null;
		}
	}
	
	/**
	 * Gets a value from any of the global variables (get/post)
	 * 
	 * @param string $param
	 *        	The key of the value
	 */
	public static function gGlobalParam($param) {
		
		if (key_exists ( $param, crm::gInstance ()->get )) {
			
			return crm::gInstance ()->get [$param];
		} else if (key_exists ( $param, crm::gInstance ()->post )) {
			
			return crm::gInstance ()->post [$param];
		}
		
		return null;
	}
	
	/**
	 * Gets the current instance object of crm.
	 * 
	 * @return crm
	 */
	public static function gInstance() {
		
		return crm::$current;
	}
	
	/**
	 * Aggressively cleans the given value.
	 * 
	 * @param string $value        	
	 * @return string
	 */
	public static function clean($value) {
		
		return htmlspecialchars ( strip_tags ( $value ) );
	}
	
	/**
	 * Gets a value from the global GET
	 * 
	 * @param string $key        	
	 * @return mixed
	 */
	public function gGet($key) {
		
		return $this->$get [$key];
	}
	
	/**
	 *
	 * @see interfaces.IPlugin::gVisibility()
	 */
	public function gVisibility() {
		return PLUGIN_VISIBILITY::PR;
	}
	
	/**
	 * Set the appropriate (and available) settings in CONFIGURATION.
	 */
	public function ConfigureSettings() {
		
		/*
		 * CONFIGURATION::$ACTION = $this->config[CONFIGURATION::$ACTION];
		 * CONFIGURATION::$OBJECT = $this->config[CONFIGURATION::$OBJECT];
		 * CONFIGURATION::$IDENTI = $this->config[CONFIGURATION::$IDENTI];
		 * CONFIGURATION::$METHOD = $this->config[CONFIGURATION::$METHOD];
		 * CONFIGURATION::$PLUGINS = $this->config[CONFIGURATION::$PLUGINS];
		 * CONFIGURATION::$FALLBACK = $this->config[CONFIGURATION::$FALLBACK];
		 */
		
		foreach ( $this->config as $key => $setting ) {
			
			if (property_exists ( 'CONFIGURATION', strtoupper ( $key ) )) {
				
				CONFIGURATION::${strtoupper ( $key )} = $setting;
			} else {
				error_log ( '[' . __FILE__ . '] Failed to initalize configuration property: ' . $key, 0 );
			}
		}
		
		if (! isset ( CONFIGURATION::$EXTRA_CONF ) && is_array ( CONFIGURATION::$EXTRA_CONF )) {
			
			foreach ( $this->config [CONFIGURATION::$EXTRA_CONF] as $value ) {
				
				array_merge ( $this->config, $value );
			}
		}
	}
	
	/**
	 * Parses the available variables and fetches the appropriate action.
	 */
	function ParseVariables() {
		
		$object = crm::gGlobalParam ( CONFIGURATION::$OBJECT );
		$action = crm::gGlobalParam ( CONFIGURATION::$ACTION );
		$identi = crm::gGlobalParam ( CONFIGURATION::$IDENTI );

		$action = ! isset ( $action ) || is_null ( $action ) || empty ( $action ) ? MESSAGES::INDEX : $action;
		
		$this->SendMessage ( $action, $object, $identi, true );
	}
	
	/*
	 * Clean and set all the required variables (POST/GET).
	 */
	private function SetVariables() {
		$this->get = array ();
		$this->post = array ();
		
		foreach ( $_GET as $key => $value ) {
			
			$this->get [crm::clean ( $key )] = crm::clean ( $value );
		}
		
		foreach ( $_POST as $key => $value ) {
			
			$this->get [crm::clean ( $key )] = crm::clean ( $value );
		}
	}
	
	/**
	 * Recursively loads all the available plugins in the dir
	 * CONFIGURATION::PLUGIN_DIR
	 * 
	 * @see CONFIGURATION::PLUGIN_DIR
	 */
	private function LoadPlugins() {
		
		if (isset ( CONFIGURATION::$PLUGINS ) && is_array ( CONFIGURATION::$PLUGINS )) {
			
			foreach ( CONFIGURATION::$PLUGINS as $plugin ) {
				try {
					
					$single_slash = <<<'EOT'
\
EOT;
					
					$class = CONFIGURATION::$PLUGIN_DIR . $single_slash . str_replace ( php, null, $plugin );
					echo $class . "\n";
					$instance = new $class ();
					
					if ($this->RegisterPlugin ( $instance, $plugin )) {
						echo $class . ": Calling ->Plugin()\n";
						$instance->Plugin ();
					}

					else {
						
						unset ( $instance );
					}
				
				} catch ( Exception $e ) {
					continue;
				}
			}
			
			$this::log ( "Plugins finished loading", null );
		}
	}
	
	/**
	 * Registers a plugin.
	 * 
	 * @see interfaces.IPlugin
	 * @param
	 *        	instance
	 * @return True if successfull, otherwise (if not instance of IPlugin) false
	 */
	private function RegisterPlugin($instance, $name) {
		if ($instance instanceof IPlugin) {
			echo $name . ": Instance of IPlugin\n";
			$this->pluginlist [$name] = $instance;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Initializes all the loaded plugins.
	 */
	private function InitializePlugins() {
		
		foreach ( $this->pluginlist as $instance ) {
			$instance->Initialize ();
		}
	}
	
	/**
	 *
	 * @see interfaces.IObservable::SendMessage()
	 */
	function SendMessage($message, $object, $id, $from_public = false) {
		
		if (array_key_exists ( $message, $this->observerlist )) {
			
			foreach ( $this->observerlist [$message] as $msg => $instance ) {
				
				/**
				 * An instance of IPlugin
				 * 
				 * @var IPlugin
				 */
				$instance = $instance; // TODO: Remove this line (it is only for
				                       // ide-autocomplete/doc-support).
				
				if (($from_public && $instance->gVisibility () == PLUGIN_VISIBILITY::PU) || ! $from_public) {
					
					/**
					 * The return type of the message handler.
					 * 
					 * @var MESSAGE_RETURN_TYPE
					 */
					$return = $instance->Callback ( $object, $id, $msg );
					
					switch ($return) {
						case MESSAGE_RETURN_TYPE::STOP_CHAIN :
							break 2; // Stop the chain and return to calleé
						
						default :
							continue;
					}
				} else {
					crm::log ( "Forbidden: " . $message );
					$this->SendMessage ( MESSAGES::ERROR_404, $message, $object );
				}
			}
		}
	}
	
	/**
	 *
	 * @see interfaces.IObservable::Register()
	 * @return boolean True if the client is successfully registered.
	 */
	function Register($client, $msg) {
		
		if ($client instanceof IObserver) {
			
			if (isset ( $this->observerlist [$msg] ) && is_array ( $this->observerlist [$msg] )) {
				
				$this->observerlist [$msg] [] = $client;
				return true;
			} else {
				
				$this->observerlist [$msg] = array ();
				$this->observerlist [$msg] [] = $client;
				return true;
			}
		} else {
			return false;
		}
	}
	
	/**
	 *
	 * @see interfaces.IObservable::Unregister()
	 * @return boolean True if succesfull, or false if no
	 *         elements of $client not an instance of IObserver.
	 */
	public function Unregister($client, $msg) {
		$deleted = 0;
		
		if(key_exists($msg, $this->observerlist)) {
			
			$key = array_search($client, $this->observerlist[$msg]);
			
			if ($key !== false) {
				
				unset($this->observerlist[$msg][$key]);
				return true;
				
			}
			else {
				return false;
			}
		}
	}
	
	/**
	 *
	 * @see interfaces.IPlugin::Plugin()
	 */
	function Plugin() {
		crm::$current = $this;
		$this->config = parse_ini_file ( CONFIG_FILE );
		$this->observerlist = array ();
		$this->pluginlist = array ();
		;
	}
	
	/**
	 *
	 * @see interfaces.IPlugin::Initialize()
	 */
	function Initialize() {
		$this->SetVariables ();
		$this->ConfigureSettings ();
		$this->LoadPlugins ();
		$this->InitializePlugins ();
		$this->ParseVariables ();
	
	}
	
	/**
	 * Dynamically invokes a message whenever a method that doesn't exist is
	 * invoked.
	 * 
	 * @param string $name        	
	 * @param array $args
	 *        	0: MESSAGE_ARG_TYPE::ON, 1: MESSAGE_ARG_TYPE::ID
	 */
	public static function __callStatic($name, $args) {
		
		$func = array ();
		
		if (method_exists ( 'crm', $name )) {
			
			$func [] = 'crm';
			$func [] = $name;
			
			call_user_func ( $func );
		} else {
			
			\crm::gInstance ()->SendMessage ( $name, $args [MESSAGE_ARG_TYPE::ON], $args [MESSAGE_ARG_TYPE::ID] );
		}
	}
	
	/**
	 * Start the application.
	 */
	static function Start() {
		
		$_this = new crm ();
		$_this->Plugin ();
		$_this->Initialize ();
	}

}

?>