<?php

namespace utils;

/** 
 * Builds html-tags from data and method-name.
 * @name html
 * @author marcus
 */
class html {
	
	/**
	 * The constant name of this class.
	 * @var string
	 */
	const classname = "html";
	
	const start = "start";
	const end = "end";
	const self = "self";
	
	/**
	 * Singleton pattern:
	 * Private  constructor.
	 */
	private function __construct() {}
	
	/**
	 * Captures any and all static invokings of functions on this class and builds the html-tag corresponding to the invoked function.
	 * @param string $name The name of the method/function invoked.
	 * @param array $args An array containing the parameters used when invoking the method/function.
	 * @return string The built tag
	 */
	public static function __callStatic(string $name, array $args) {
		
		$func_array = array(html::classname, $name);
		
		if (method_exists(html::classname, $name) && is_callable($func_array)) {
			
			return call_user_func($func_array, $args);
		}
		else {

			$data = null;
			$attr = null;
			$type = null;
			$return = null;
			
			var_dump($args);
			
			switch (count($args)) {
				default:
				case 0:
					$type = html::start;
					break;
				
				case 1:
					$type = html::self;
					$data = array_shift($args);
					break;
				case 2:
					$data = array_shift($args);
					$attr = array_shift($args);
					$type = html::self;
					break;
				case 3:
					$data = array_shift($args);
					$attr = array_shift($args);
					$type = array_shift($args);
					break;
			}

			if (is_array($data)) {
				
				foreach ($data as $value) {

					$return .= html::$name($value, $attr, $type);
				}
				
				return $return;
			}

			switch ($type) {
				
				case html::start:
					return html::builder($name);
					break;
				case html::end:
					return html::builder($name, false, true);
					break;
				case html::self:
					return html::builder($name, true, false, $attr);
					break;
				
				default:
					$return .= html::builder($name, false, false, $attr);
					$return .= $data;
					$return .= html::builder($name, false, true);
					break;
			}

			return $return;
		}
	}
	
	/**
	 * Builds an html-tag with the specified attribute and name.
	 * @param string $name The name of the tag
	 * @param boolean $is_enclosed Indicates whether the tag should be self-enclosing or not
	 * @param boolean $is_end Indicates whether the tag should be the closing end.
	 * @param string $attr The attribute of the opening and/or self-enclosing tag.
	 * @return string
	 */
	private static function builder($name, $is_enclosed = false, $is_end = false, $attr = null) {
		
		if ($is_enclosed) {
			
			return '<' . $name . ' ' . $attr . '/>';
		}
		elseif($is_end) {
			
			return '</' . $name . '>';
		}
		else {
			
			return '<' . $name . ' ' . $attr . '>';
		}
	}
}

?>