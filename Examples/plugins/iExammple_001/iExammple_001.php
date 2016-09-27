<?php class iExammple001PluginClass extends TGR_PluginClass {
	static $properties = array(
							   	'Name'=>'iExammple001',
								'Major version'=>'1.0',
								'Minor Version'=>'0.9',
								'Build'=>'1.0 Cd',
								'Copyright'=> '2013-14 by rankwatch.com',
							   );
	private $config = array();
	private $reqMapper = false;
	private $reqOut = '';
	private $reqParams = array();
	function getReqOut() {
		return $this->reqOut;
	}
	function getReqMapper() {
		return $this->reqMapper;
	}
	function getReqParams() {
		return $this->reqParams;
	}
	function getConfig() {
		return $this->config;
	}
	function onBeforeAppInit () {	

		$load_config = $this->curr_path  . 'configs' . DS  . 'iRestApi.conf';
		$load_config = @parse_ini_file($load_config, true);
		if ( $load_config === false || $load_config === null ) 
			return false;

		$routers = array(  app_get_controller() . '/' . app_get_action(),
						   app_get_controller()
						);
		$load_config['main']['output'] = explode("|", $load_config['main']['output']);
		$this->reqOut = current( $load_config['main']['output']);
		// check matched router
		$found = false;
		foreach($routers as $idx=>$rt) {
			foreach ($load_config['routes'] as $key => $value) {
				if ($rt===$key) {
					$this->reqMapper = $value;
					$found = true;
					break 2; 
				}
			}
		}

		#vdd($load_config['routes'], $this->reqMapper);
		//
		if ( !$found ) {
			$this->reqMapper = false ;
			unset($load_config);
			return ;
		}
		$params = app_get_params();
		if (!$idx) {
			if ( isset($params[0]) ) {
				$this->reqOut = $params[0];
				unset($params[0]);
				$params = array_values($params); // reset
			}
		} else {
			$this->reqOut = app_get_action();
		}
		#vdid ($this->reqOut, $this->reqMapper,  $idx, app_get_controller(),  app_get_action(), app_get_params(),   $load_config); 
		$this->config = $load_config;
		$this->reqParams = $params;
		$load_config = $params =  null;
		unset($load_config, $params);

		//
		if ( app_auth_set()  )  {
			app_auth_access('allows', array('irestapi'=>array('*'=>0, 'test'=>0 )) );
		}

		// register routes controler 
		$properties  = array( 'default_action'  => 'index',
							  'notfound_action' => array( 
														  'method' =>'myNotFoundAction'	
														  
														  												  )
							  );				
		app_register_controller( $this ,'irestapi', $properties ) ;

		//
		app_set_controller('irestapi') ;
		app_set_action('dispatch') ;

		$me = $this;
		// set visible to all app
		app_set_to('app', 'iRestApi', $me );
		$c= app_set_to('app', 'iRestApi', $me );
	
	}	
	function onRenderRequest() {

	}
	function onBeforeAppLoad () {

		if ( $this->reqMapper == false && !$this->config['main']['exclusive'] )
			return ;

		// die("eeee");
	}
	function onBeforeLoad () {
		// echo "onBeforeLoad";
	}	
	function onBeforeRender () {
		//echo "onBeforeRender";	
	}
	function onAfterRender () {
		//echo "onAfterRender";	
	}
	function onBeforeDispatch(&$out = null, $delete = false, $p1 =1 , $p2 =2 , $p3 =3 , $p4 = 4 , $p5 =5  ){
				
	}	 
	
	function onHookRequest () {			
	}	
	
	
	
	// Ajax Call
	function getPluginInfo ($eventTarget,$events,$params,&$gResponse) {
		$info ='Plugin Info gg: '.chr(10);
		$info .='========================= '.chr(10);
		foreach( $this->properties as $n=>$p)
			$info .=" $n  : $p" .chr(10);
		$gResponse->alert($info);
	}
	function renderSection () {
		global $gResponse;
		$theSectionContents =  app_render_plugin_section('section1', array('name'=>'The India'), $this );
		$gResponse->innerHTML('#plgRenderSection',$theSectionContents);
	}
	
}?>