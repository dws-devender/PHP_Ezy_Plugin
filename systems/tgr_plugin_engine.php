<?php
	require_once (dirname(__FILE__) . '/tgr_plugin.class.php');
	require_once (dirname(__FILE__) . '/tgr_shutdown_scheduler.php');
	require_once (dirname(__FILE__) . '/tgr.php');
	require_once (dirname(__FILE__) . '/tgr_app_share.php');	
	$gShutdownScheduler = new TGR_ShutdownScheduler();
	
	# Load config
	$config_path =  PHP_Ezy_Plugin_Dir . 'config.xml';
	if( !( file_exists($config_path) and is_readable($config_path)  and ($gDomConfig = new DOMDocument())!=false and $gDomConfig->loadXML(file_get_contents($config_path))  &&  $gDomConfig ) ){
		return ;
	}

	# Activate Plugins
	$plugins =  $gDomConfig->getElementsByTagName('plugin');
	foreach( $plugins as $plugin  ) {
		if ( !(int) $plugin->getAttribute('status') ) 	
			continue;		
		$mode = $plugin->getAttribute('mode') ;
		$guid = $plugin->getAttribute('guid');
		$plugin_name = $plugin->getAttribute('name');
		$plugin_file = PHP_Ezy_Plugin_Dir . $plugin_name . PHP_Ezy_Plugin_DS .  "{$plugin_name}.php"; 
		if ( !is_readable($plugin_file))
			continue;
		require_once($plugin_file);
		$plugin_clsname = preg_replace('/[^a-z0-9]/is', '', $plugin_name); 
		$plugin_clsname = "{$plugin_clsname}PluginClass";
		if ( !class_exists($plugin_clsname))
			continue;
		# Get config data
		$plugin_cache = PHP_Ezy_Plugin_Cache_Dir . md5($plugin_name) . '.dat';
		$resume_configs = is_readable($plugin_cache) ? @unserialize(file_get_contents($plugin_cache)) : array();
		if ( !$resume_configs ) {
			$resume_configs = array();
			$configs = $plugin->getElementsByTagName('config');
			foreach ($configs as $config) {
				$v = $config->nodeValue;
				$t = $config->getAttribute('type');
				$n = $config->getAttribute('name');	
				$resume_configs[$n] =($t == 'int' ? intval($t) : ($t == 'long' ? intval($v) : ($t == 'double' ? floatval($v) : ($t == 'float' ? floatval($t) : ($t == 'json' ? json_decode($v) : ($t == 'serialize' ? unserialize($v) : ($t == 'bool' ? (in_array($v, array('1', 'true', 'on' )) ? true : false) : $v) ) )  ) )  ) );
			}
			$resume_configs['__DIRTY__'] = 1;
			$resume_configs['__GUID__'] = $guid;
			$resume_configs['__MODE__'] = $mode;
			$resume_configs['__NAME__'] = $plugin_name;
		}
		$resume_configs = !$resume_configs?array():$resume_configs;
		$resume_configs['__CACHE__'] = $plugin_cache;
		$resume_configs['__DIR__'] = PHP_Ezy_Plugin_Dir . $plugin_name . PHP_Ezy_Plugin_DS;

		$_t = new $plugin_clsname($resume_configs);
		$c = &$_t;
		######################################

		# call onHookRequest
		$c->onHookRequest();

		######################################
		$hk = $_t ;// array(&$_t, 'onBeforeAppInit');
		TGR::register_hook('onBeforeAppInit', $hk, 10, array(), TGR::HOOK_RETURN);
		die;
			
			app_register_hook('onRenderRequest', array($c,'onRenderRequest'));	
			
			//
			app_register_hook('onBeforeAppLoad', array($c,'onBeforeAppLoad'));
			
		
			
			// 
			app_register_hook('onBeforeLoad', array($c,'onBeforeLoad'));
			
			
			// 
			app_register_hook('onBeforeRender', array($c,'onBeforeRender'));
			
			// 
			app_register_hook('onAfterRender', array($c,'onAfterRender'));
			
			
			// 
			app_register_hook('onBeforeDispatch', array($c,'onBeforeDispatch'));
			
			// login before hooks
			app_register_hook('onBeforeAppLogIn', array($c,'onBeforeAppLogIn'));

			// login after hooks
			app_register_hook('onAfterAppLogIn', array($c,'onAfterAppLogIn'));

			// logout before hooks
			app_register_hook('onBeforeAppLogOut', array($c,'onBeforeAppLogOut'));

			// logout after hooks
			app_register_hook('onAfterAppLogOut', array($c,'onAfterAppLogOut'));

		######################################

	}	

	print_r($gDomConfig);
	die;

	$t1 = new t1();
	$h1 = array($t1, 'f1');
	# var_dump(is_callable($h1)); die;
	TGR::register_hook('OnBefore Save');
?>
<?php

function test() {

}
class t1{
	function f1() {

	}
}
?>