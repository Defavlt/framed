<?php
namespace interfaces;

/**
 * Makes an object legible for observing another object.
 * @author marcus
 *        
 */
interface IObserver {

	function Callback($on, $id);
}

?>