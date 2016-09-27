<?php
abstract class TGR_PluginClass {
	static  $properties = array(
							'name' => '',
							'version'=> '',
							'copyright'=> '',
							'website' => ''
					   );
	protected  $_data;
	abstract function onHookRequest() ;	
	final function __construct($resume) {
		$this->_data =  $resume ;
	}
	final function __destruct() {
		if ( !$this->_data['__DIRTY__'] )
			return ;
		$this->_data['__DIRTY__'] = 0;
		$this->_data['properties'] = self::$properties;
		$_data =  serialize($this->_data);
		@file_put_contents($this->_data['__CACHE__'], $_data, LOCK_EX);
	}
	final function get($key) {
		return isset($this->_data[$key])?$this->_data[$key]:NULL;
	}
	final function set($key, $val) {
		if (! isset($this->_data[$key]))
			return false;
		$this->_data['__DIRTY__'] = 1;
		$this->_data[$key] = $val;
	}
	/* AppInit */
	function onBeforeAppInit(){}
	function onAfterAppInit(){}
	
	/* AppLoad */
	function onBeforeAppLoad(){}
	function onAfterAppLoad(){} 
	
	/* Render page */
	function onBeforeRender(){}
	function onAfterRender(){}	
	
	/* login */
	function onBeforeAppLogIn(){}
	function onAfterAppLogIn(){}
	
	/* logout */
	function onBeforeAppLogOut(){}
	function onAfterAppLogOut(){}
	
	/* On Shutdown */
	function onAppShutdown(){}
	
	/* Dispatch */ 
	function onBeforeDispatch(){}
	function onAfterDispatch(){}

	/* HTTP Header  */ 
	function onBeforeHeaderSent(){}
	function onAfterHeaderSent(){} 
}
?>